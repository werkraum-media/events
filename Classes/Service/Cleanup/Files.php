<?php

namespace Wrm\Events\Service\Cleanup;

/*
 * Copyright (C) 2019 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Files
{
    public function deleteAll()
    {
        $this->delete($this->getFilesFromDb());
    }

    public function deleteDangling()
    {
        $this->delete($this->getFilesFromDb(function (QueryBuilder $queryBuilder) {
            $queryBuilder->leftJoin(
                'file',
                'sys_file_reference',
                'reference',
                $queryBuilder->expr()->eq('file.uid', $queryBuilder->quoteIdentifier('reference.uid_local'))
            );
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull('reference.uid'),
                    $queryBuilder->expr()->eq('reference.deleted', 1),
                    $queryBuilder->expr()->eq('reference.hidden', 1)
                )
            );
        }));
    }

    private function delete(array $filesToDelete)
    {
        $uidsToRemove = [];

        foreach ($filesToDelete as $fileToDelete) {
            $this->deleteFromFal($fileToDelete['storage'], $fileToDelete['identifier']);
            $uidsToRemove[] = $fileToDelete['uid'];
        }

        $this->deleteFromDb(...$uidsToRemove);
    }

    private function getFilesFromDb(callable $whereGenerator = null): array
    {
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        $queryBuilder->getRestrictions()->removeAll();

        $queryBuilder->select('file.identifier', 'file.storage', 'file.uid')
            ->from('sys_file', 'file')
            ->where($queryBuilder->expr()->like(
                'file.identifier',
                $queryBuilder->createNamedParameter('/staedte/%/events/%')
            ));

        if ($whereGenerator !== null) {
            $whereGenerator($queryBuilder);
        }

        return $queryBuilder->execute()->fetchAll();
    }

    private function deleteFromFal(int $storageUid, string $filePath)
    {
        /* @var ResourceStorage $storage */
        $storage = GeneralUtility::makeInstance(StorageRepository::class)
            ->findByUid($storageUid);

        if ($storage->hasFile($filePath) === false) {
            return;
        }

        $storage->deleteFile($storage->getFile($filePath));
    }

    private function deleteFromDb(int ...$uids)
    {
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');

        $queryBuilder->delete('sys_file')
            ->where('uid in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }
}

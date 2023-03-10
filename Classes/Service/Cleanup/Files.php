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
use TYPO3\CMS\Core\Resource\StorageRepository;

class Files
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var StorageRepository
     */
    private $storageRepository;

    public function __construct(
        ConnectionPool $connectionPool,
        StorageRepository $storageRepository
    ) {
        $this->connectionPool = $connectionPool;
        $this->storageRepository = $storageRepository;
    }

    public function deleteDangling(): void
    {
        $this->markFileReferencesDeletedIfForeignRecordIsMissing();
        $this->deleteFilesWithoutProperReference();
    }

    private function markFileReferencesDeletedIfForeignRecordIsMissing(): void
    {
        $referencesQuery = $this->connectionPool
            ->getQueryBuilderForTable('sys_file_reference');
        $referencesQuery->getRestrictions()->removeAll();
        $referencesQuery->select(
            'uid',
            'uid_foreign',
            'tablenames'
        );
        $referencesQuery->from('sys_file_reference');
        $referencesQuery->where(
            $referencesQuery->expr()->like(
                'tablenames',
                $referencesQuery->createNamedParameter('tx_events_domain_model_%')
            )
        );
        $referencesQuery->orderBy('tablenames');
        $referencesQuery->addOrderBy('uid_foreign');

        $references = $referencesQuery->execute();

        $uidsPerTable = [];
        $referenceUidsToMarkAsDeleted = [];

        while ($reference = $references->fetch()) {
            if (is_array($reference) === false) {
                continue;
            }

            $uidsPerTable[(string)$reference['tablenames']][$reference['uid']] = $reference['uid_foreign'];
        }

        foreach ($uidsPerTable as $tableName => $records) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable($tableName);
            $queryBuilder->getRestrictions()->removeAll();
            $queryBuilder->select('uid');
            $queryBuilder->from($tableName);
            $queryBuilder->where($queryBuilder->expr()->in('uid', $records));
            $referenceUidsToMarkAsDeleted = array_merge(
                $referenceUidsToMarkAsDeleted,
                array_keys(array_diff($records, $queryBuilder->execute()->fetchAll(\PDO::FETCH_COLUMN)))
            );
        }

        if ($referenceUidsToMarkAsDeleted === []) {
            return;
        }

        $updateQuery = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $updateQuery->update('sys_file_reference');
        $updateQuery->where($updateQuery->expr()->in('uid', $referenceUidsToMarkAsDeleted));
        $updateQuery->set('deleted', '1');
        $updateQuery->execute();
    }

    private function deleteFilesWithoutProperReference(): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->select('file.identifier', 'file.storage', 'file.uid')
            ->from('sys_file', 'file')
            ->leftJoin(
                'file',
                'sys_file_reference',
                'reference',
                'reference.uid_local = file.uid'
            )
            ->where($queryBuilder->expr()->eq(
                'reference.deleted',
                1
            ))
            ->andWhere($queryBuilder->expr()->like(
                'reference.tablenames',
                $queryBuilder->createNamedParameter('tx_events_domain_model_%')
            ))
            ;
        /** @var array{int: array{storage: int, identifier: string, uid: int}} $filesToDelete */
        $filesToDelete = $queryBuilder->execute()->fetchAll();

        $uidsToRemove = [];
        foreach ($filesToDelete as $fileToDelete) {
            $this->deleteFromFal((int) $fileToDelete['storage'], (string) $fileToDelete['identifier']);
            $uidsToRemove[] = (int) $fileToDelete['uid'];
        }

        $this->deleteFromDb(...$uidsToRemove);
    }

    private function deleteFromFal(int $storageUid, string $filePath): void
    {
        $storage = $this->storageRepository->findByUid($storageUid);

        if ($storage === null || $storage->hasFile($filePath) === false) {
            return;
        }

        $storage->deleteFile($storage->getFile($filePath));
    }

    private function deleteFromDb(int ...$uids): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->delete('sys_file')
            ->where('uid in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder->delete('sys_file_reference')
            ->where('uid_local in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_metadata');
        $queryBuilder->delete('sys_file_metadata')
            ->where('file in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }
}

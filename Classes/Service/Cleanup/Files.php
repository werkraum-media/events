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
        // Remove file relations removed via import
        $referencesQuery->orWhere(
            $referencesQuery->expr()->andX(
                $referencesQuery->expr()->eq(
                    'tablenames',
                    $referencesQuery->createNamedParameter('')
                ),
                $referencesQuery->expr()->eq(
                    'fieldname',
                    $referencesQuery->createNamedParameter('')
                ),
                $referencesQuery->expr()->eq(
                    'sorting_foreign',
                    $referencesQuery->createNamedParameter('0')
                ),
                $referencesQuery->expr()->eq(
                    'uid_foreign',
                    $referencesQuery->createNamedParameter('0')
                )
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

            if ($reference['tablenames'] === '') {
                $referenceUidsToMarkAsDeleted[] = $reference['uid'];
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
        $filesToDelete = $this->filterPotentialFilesToDelete($this->getPotentialFilesToDelete());

        foreach ($filesToDelete as $file) {
            $this->deleteFromFal((int)$file['storage'], (string)$file['identifier']);
        }

        $this->deleteFromDb(...array_keys($filesToDelete));
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

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_metadata');
        $queryBuilder->delete('sys_file_metadata')
            ->where('file in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();

        $this->deleteReferences();
    }

    private function deleteReferences(): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->delete('sys_file_reference')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like(
                        'tablenames',
                        $queryBuilder->createNamedParameter('tx_events_domain_model_%')
                    ),
                    $queryBuilder->expr()->eq(
                        'tablenames',
                        $queryBuilder->createNamedParameter('')
                    )
                )
            )
            ->andWhere($queryBuilder->expr()->eq(
                'deleted',
                1
            ))
        ;
        $queryBuilder->execute();
    }

    /**
     * @return array<int, array{storage: int, identifier: string}> Index is file uid.
     */
    private function getPotentialFilesToDelete(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('file.uid', 'file.storage', 'file.identifier')
            ->from('sys_file', 'file')
            ->leftJoin(
                'file',
                'sys_file_reference',
                'reference',
                'reference.uid_local = file.uid'
            )
            ->where($queryBuilder->expr()->like(
                'reference.tablenames',
                $queryBuilder->createNamedParameter('tx_events_domain_model_%')
            ))
            ->orWhere($queryBuilder->expr()->eq(
                'reference.tablenames',
                $queryBuilder->createNamedParameter('')
            ))
            ->groupBy('file.uid')
        ;

        return $queryBuilder->execute()->fetchAllAssociativeIndexed();
    }

    /**
     * @param array<int, array{storage: int, identifier: string}> $files
     *
     * @return array<int, array{storage: int, identifier: string}> Index is file uid.
     */
    private function filterPotentialFilesToDelete(array $files): array
    {
        $filesToDelete = [];
        $filesToKeep = [];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('*')
            ->from('sys_file_reference', 'reference')
            ->where($queryBuilder->expr()->in(
                'uid_local',
                $queryBuilder->createNamedParameter(array_keys($files), Connection::PARAM_INT_ARRAY)
            ))
        ;

        foreach ($queryBuilder->execute() as $reference) {
            $file = [];
            $fileUid = (int)$reference['uid_local'];

            if (
                (
                    str_starts_with($reference['tablenames'], 'tx_events_domain_model_')
                    || $reference['tablenames'] === ''
                ) && $reference['deleted'] == 1
            ) {
                $file = $files[$fileUid] ?? [];
            } else {
                $filesToKeep[$fileUid] = $fileUid;
            }

            if ($file === []) {
                continue;
            }

            $filesToDelete[$fileUid] = $file;
        }

        return array_diff_key($filesToDelete, $filesToKeep);
    }
}

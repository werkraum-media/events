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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Database
{
    public const DATE_TABLE = 'tx_events_domain_model_date';
    public const EVENT_TABLE = 'tx_events_domain_model_event';
    public const ORGANIZER_TABLE = 'tx_events_domain_model_organizer';

    public function truncateTables(string ...$tableNames): void
    {
        foreach ($tableNames as $tableName) {
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable($tableName)
                ->truncate($tableName);
        }
    }

    public function getDeletionStructureForEvents(): array
    {
        $dataStructure = [static::EVENT_TABLE => []];

        foreach ($this->getAllRecords(static::EVENT_TABLE) as $recordToDelete) {
            $dataStructure[static::EVENT_TABLE][$recordToDelete] = ['delete' => 1];
        }

        return $dataStructure;
    }

    private function getAllRecords(string $tableName): array
    {
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->createQueryBuilder();

        $records = $queryBuilder->select('uid')
            ->from($tableName)
            ->execute()
            ->fetchAll();

        return array_map(function (array $record) {
            return $record['uid'];
        }, $records);
    }

    public function getPastDates(): array
    {
        $midnightToday = new \DateTimeImmutable('midnight today');

        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(static::DATE_TABLE)
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('uid')
            ->from(static::DATE_TABLE)
            ->where($queryBuilder->expr()->lte(
                'end',
                $queryBuilder->createNamedParameter($midnightToday->format('Y-m-d H:i:s'))
            ))
            ->execute()
            ->fetchAll();

        return array_map(function (array $record) {
            return $record['uid'];
        }, $records);
    }

    public function deleteDates(int ...$uids)
    {
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(static::DATE_TABLE);

        $queryBuilder->delete(static::DATE_TABLE)
            ->where('uid in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function getDeletionStructureForEventsWithoutDates(): array
    {
        $dataStructure = [static::EVENT_TABLE => []];
        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(static::EVENT_TABLE)
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder->select('event.uid')
            ->from(static::EVENT_TABLE, 'event')
            ->leftJoin('event', static::DATE_TABLE, 'date', $queryBuilder->expr()->eq('date.event', 'event.uid'))
            ->where($queryBuilder->expr()->isNull('date.uid'))
            ->execute()
            ->fetchAll();

        foreach ($records as $record) {
            $dataStructure[static::EVENT_TABLE][$record['uid']] = ['delete' => 1];
        }
        return $dataStructure;
    }
}

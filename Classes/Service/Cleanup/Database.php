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
use TYPO3\CMS\Core\DataHandling\DataHandler;

class Database
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    private const DATE_TABLE = 'tx_events_domain_model_date';
    private const EVENT_TABLE = 'tx_events_domain_model_event';
    private const ORGANIZER_TABLE = 'tx_events_domain_model_organizer';

    public function __construct(
        ConnectionPool $connectionPool,
        DataHandler $dataHandler
    ) {
        $this->connectionPool = $connectionPool;
        $this->dataHandler = $dataHandler;
    }

    public function truncateTables(): void
    {
        $tableNames = [
            Database::DATE_TABLE,
            Database::ORGANIZER_TABLE,
            Database::EVENT_TABLE,
        ];

        foreach ($tableNames as $tableName) {
            $this->connectionPool
                ->getConnectionForTable($tableName)
                ->truncate($tableName);
        }
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->delete('sys_category_record_mm')
            ->where($queryBuilder->expr()->like(
                'tablenames',
                $queryBuilder->createNamedParameter('tx_events_domain_model_%')
            ))
            ->execute();
    }

    public function getPastDates(): array
    {
        $queryBuilder = $this->connectionPool
            ->getConnectionForTable(self::DATE_TABLE)
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $midnightToday = new \DateTimeImmutable('midnight today');
        $records = $queryBuilder->select('uid')
            ->from(self::DATE_TABLE)
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

    public function deleteDates(int ...$uids): void
    {
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable(self::DATE_TABLE);

        $queryBuilder->delete(self::DATE_TABLE)
            ->where('uid in (:uids)')
            ->setParameter(':uids', $uids, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function deleteEventsWithoutDates(): void
    {
        $queryBuilder = $this->connectionPool
            ->getConnectionForTable(self::EVENT_TABLE)
            ->createQueryBuilder();

        $queryBuilder->getRestrictions()->removeAll();

        $recordUids = $queryBuilder->select('event.uid')
            ->from(self::EVENT_TABLE, 'event')
            ->leftJoin('event', self::DATE_TABLE, 'date', $queryBuilder->expr()->eq('date.event', 'event.uid'))
            ->where($queryBuilder->expr()->isNull('date.uid'))
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);

        $dataStructure = [self::EVENT_TABLE => []];
        foreach ($recordUids as $recordUid) {
            $dataStructure[self::EVENT_TABLE][$recordUid] = ['delete' => 1];
        }

        $dataHandler = clone $this->dataHandler;
        $dataHandler->start([], $dataStructure);
        $dataHandler->process_cmdmap();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->delete('sys_category_record_mm')
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->like(
                    'tablenames',
                    $queryBuilder->createNamedParameter('tx_events_domain_model_%')
                ),
                $queryBuilder->expr()->in(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($recordUids, Connection::PARAM_INT_ARRAY)
                )
            ))
            ->execute();
    }
}

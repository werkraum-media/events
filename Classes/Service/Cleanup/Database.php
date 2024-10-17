<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\Cleanup;

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

use DateTimeImmutable;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final class Database
{
    private const DATE_TABLE = 'tx_events_domain_model_date';
    private const EVENT_TABLE = 'tx_events_domain_model_event';
    private const ORGANIZER_TABLE = 'tx_events_domain_model_organizer';

    public function __construct(
        private readonly ConnectionPool $connectionPool
    ) {
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
                ->truncate($tableName)
            ;
        }
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->delete('sys_category_record_mm')->where($queryBuilder->expr()->like(
            'tablenames',
            $queryBuilder->createNamedParameter('tx_events_domain_model_%')
        ))->executeStatement();
    }

    public function deletePastDates(): void
    {
        $queryBuilder = $this->connectionPool
            ->getConnectionForTable(self::DATE_TABLE)
            ->createQueryBuilder()
        ;

        $queryBuilder->getRestrictions()->removeAll();

        $midnightToday = new DateTimeImmutable('midnight today');
        $queryBuilder->delete(self::DATE_TABLE)->where($queryBuilder->expr()->lte(
            'end',
            $queryBuilder->createNamedParameter($midnightToday->format('U'))
        ))->executeStatement();
    }

    public function deleteEventsWithoutDates(): void
    {
        $queryBuilder = $this->connectionPool
            ->getConnectionForTable(self::EVENT_TABLE)
            ->createQueryBuilder()
        ;

        $queryBuilder->getRestrictions()->removeAll();

        $recordUids = $queryBuilder->select('event.uid')
            ->from(self::EVENT_TABLE, 'event')
            ->leftJoin('event', self::DATE_TABLE, 'date', $queryBuilder->expr()->eq('date.event', 'event.uid'))->where($queryBuilder->expr()->isNull('date.uid'))->executeQuery()
            ->fetchFirstColumn()
        ;

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::EVENT_TABLE);
        $queryBuilder->delete(self::EVENT_TABLE);
        $queryBuilder->where($queryBuilder->expr()->in(
            'uid',
            $queryBuilder->createNamedParameter($recordUids, Connection::PARAM_INT_ARRAY)
        ));
        $queryBuilder->executeStatement();

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
        $queryBuilder->delete('sys_category_record_mm')->where($queryBuilder->expr()->and($queryBuilder->expr()->like(
            'tablenames',
            $queryBuilder->createNamedParameter('tx_events_domain_model_%')
        ), $queryBuilder->expr()->in(
            'uid_foreign',
            $queryBuilder->createNamedParameter($recordUids, Connection::PARAM_INT_ARRAY)
        )))->executeStatement();
    }
}

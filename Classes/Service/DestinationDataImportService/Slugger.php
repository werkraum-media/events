<?php

declare(strict_types=1);

/*
 * Copyright (C) 2022 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Generator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Events\Service\DestinationDataImportService\Slugger\Registry;
use WerkraumMedia\Events\Service\DestinationDataImportService\Slugger\SluggerType;

final class Slugger
{
    public function __construct(
        private readonly Registry $registry,
        private readonly ConnectionPool $connectionPool
    ) {
    }

    public function update(string $tableName): void
    {
        $sluggerType = $this->registry->get($tableName);
        foreach ($this->getRecords($sluggerType) as $record) {
            $this->updateRecord($sluggerType, $record);
        }
    }

    /**
     * @return Generator<array>
     */
    private function getRecords(SluggerType $sluggerType): Generator
    {
        $tableName = $sluggerType->getSupportedTableName();
        $slugColumn = $sluggerType->getSlugColumn();
        $queryBuilder = $this->getQueryBuilder($tableName);

        $statement = $queryBuilder->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq($slugColumn, $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull($slugColumn)
                )
            )
            ->executeQuery()
        ;

        foreach ($statement->iterateAssociative() as $record) {
            yield $record;
        }
    }

    private function updateRecord(SluggerType $sluggerType, array $record): void
    {
        $tableName = $sluggerType->getSupportedTableName();
        $record = $sluggerType->prepareRecordForSlugGeneration($record);
        $slug = $this->getSlugHelper($sluggerType)->generate($record, (int)$record['pid']);

        $queryBuilder = $this->getQueryBuilder($tableName);
        $queryBuilder->update($tableName)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter((int)$record['uid'])
                )
            )
            ->set($sluggerType->getSlugColumn(), $slug)
        ;
        $queryBuilder->executeStatement();
    }

    private function getSlugHelper(
        SluggerType $sluggerType
    ): SlugHelper {
        $tableName = $sluggerType->getSupportedTableName();
        $column = $sluggerType->getSlugColumn();

        return GeneralUtility::makeInstance(
            SlugHelper::class,
            $tableName,
            $column,
            $GLOBALS['TCA'][$tableName]['columns'][$column]['config']
        );
    }

    private function getQueryBuilder(
        string $tableName
    ): QueryBuilder {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder;
    }
}

<?php

declare(strict_types=1);

namespace Wrm\Events\Domain\Repository;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Repository;

class CategoryRepository extends Repository
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var DataMapper
     */
    protected $dataMapper;

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    public function injectDataMapper(DataMapper $dataMapper): void
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * @return array<Category>
     */
    public function findAllCurrentlyAssigned(): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $qb->select('category.*');
        $qb->from('tx_events_domain_model_event', 'event');

        $qb->leftJoin(
            'event',
            'sys_category_record_mm',
            'mm',
            'event.uid = mm.uid_foreign'
            . ' AND mm.tablenames = ' . $qb->createNamedParameter('tx_events_domain_model_event')
            . ' AND mm.fieldname = ' . $qb->createNamedParameter('categories')
        );

        $qb->leftJoin(
            'mm',
            'sys_category',
            'category',
            'category.uid = mm.uid_local'
            . ' AND mm.tablenames = ' . $qb->createNamedParameter('tx_events_domain_model_event')
            . ' AND mm.fieldname = ' . $qb->createNamedParameter('categories')
        );

        $qb->where($qb->expr()->neq('category.uid', $qb->createNamedParameter(0)));
        $qb->orderBy('category.title', 'asc');
        $qb->groupBy('category.uid');

        return $this->dataMapper->map(
            Category::class,
            $qb->execute()->fetchAll()
        );
    }
}

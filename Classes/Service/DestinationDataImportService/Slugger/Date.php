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

namespace WerkraumMedia\Events\Service\DestinationDataImportService\Slugger;

use TYPO3\CMS\Core\Database\ConnectionPool;

class Date implements SluggerType
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    public function __construct(
        ConnectionPool $connectionPool
    ) {
        $this->connectionPool = $connectionPool;
    }

    public function prepareRecordForSlugGeneration(array $record): array
    {
        $start = new \DateTimeImmutable('@' . $record['start']);

        $record['event-title'] = $this->getEventTitle((int)$record['event']);
        $record['start'] = $start->format('Y-m-d');
        $record['start-with-time'] = $start->format('Y-m-d\TH-i-s');
        return $record;
    }

    public function getSlugColumn(): string
    {
        return 'slug';
    }

    public function getSupportedTableName(): string
    {
        return 'tx_events_domain_model_date';
    }

    private function getEventTitle(int $eventUid): string
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $qb->select('title');
        $qb->from('tx_events_domain_model_event');
        $qb->where($qb->expr()->eq('uid', $eventUid));
        $title = $qb->execute()->fetchOne();
        if (is_string($title)) {
            return $title;
        }

        return '';
    }
}

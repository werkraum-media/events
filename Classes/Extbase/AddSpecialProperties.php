<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Extbase;

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
use TYPO3\CMS\Extbase\Event\Persistence\AfterObjectThawedEvent;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use WerkraumMedia\Events\Domain\Model\Date;

final class AddSpecialProperties
{
    /**
     * Internal info to speed things up if we know there are none.
     */
    private bool $doPostponedDatesExist = true;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly DataMapper $dataMapper
    ) {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_date');
        $qb->count('uid');
        $qb->from('tx_events_domain_model_date');
        $qb->where($qb->expr()->gt('postponed_date', $qb->createNamedParameter(0)));
        $this->doPostponedDatesExist = $qb->executeQuery()->fetchOne() > 0;
    }

    public function __invoke(AfterObjectThawedEvent $event): void
    {
        if (
            $this->doPostponedDatesExist
            && $event->getObject() instanceof Date
        ) {
            /** @var Date $date */
            $date = $event->getObject();

            $localizedUid = $date->_getProperty('_localizedUid');
            if (is_numeric($localizedUid) === false) {
                return;
            }

            $date->_setProperty('originalDate', $this->getOriginalDate((int)$localizedUid));
        }
    }

    private function getOriginalDate(int $uidOfReferencedDate): ?Date
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_date');
        $qb->select('*');
        $qb->from('tx_events_domain_model_date');
        $qb->where($qb->expr()->eq('postponed_date', $uidOfReferencedDate));
        $qb->setMaxResults(1);

        $result = $qb->executeQuery()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        $dates = $this->dataMapper->map(Date::class, [$result]);
        return $dates[0] ?? null;
    }
}

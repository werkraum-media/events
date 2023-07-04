<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
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

namespace Wrm\Events\Updates;

use Generator;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use Wrm\Events\Domain\Model\Location;

final class MigrateDuplicateLocations implements UpgradeWizardInterface
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

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Remove duplicate locations of EXT:event';
    }

    public function getDescription(): string
    {
        return 'Checks for duplicates and reduces them to one entry, fixing relations to events.';
    }

    public function updateNecessary(): bool
    {
        return true;
    }

    public function executeUpdate(): bool
    {
        $duplicates = [];

        foreach ($this->getLocations() as $location) {
            $locationObject = $this->buildObject($location);
            if ($locationObject->getGlobalId() === $location['global_id']) {
                continue;
            }

            $uid = (int)$location['uid'];
            $matchingLocations = $this->getMatchingLocations(
                $locationObject->getGlobalId(),
                $uid
            );

            // Already have entries for the new id, this one is duplicate
            if ($matchingLocations !== []) {
                $duplicates[$uid] = $matchingLocations[0];
                continue;
            }

            // No duplicates, update this one
            $this->updateLocation($locationObject, $uid);
        }

        $this->removeDuplicates(array_keys($duplicates));
        $this->updateRelations($duplicates);
        return true;
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][self::class] = self::class;
    }

    /**
     * @return Generator<array>
     */
    private function getLocations(): Generator
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $queryBuilder->select(
            'name',
            'street',
            'zip',
            'city',
            'district',
            'country',
            'phone',
            'latitude',
            'longitude',
            'global_id',
            'uid',
            'sys_language_uid'
        );
        $queryBuilder->from('tx_events_domain_model_location');
        $queryBuilder->orderBy('uid', 'asc');
        $result = $queryBuilder->execute();

        foreach ($result->fetchAllAssociative() as $location) {
            yield $location;
        }
    }

    private function getMatchingLocations(
        string $globalId,
        int $uid
    ): array {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $queryBuilder->select('uid');
        $queryBuilder->from('tx_events_domain_model_location');
        $queryBuilder->where($queryBuilder->expr()->eq('global_id', $queryBuilder->createNamedParameter($globalId)));
        $queryBuilder->andWhere($queryBuilder->expr()->neq('uid', $queryBuilder->createNamedParameter($uid)));

        return $queryBuilder->execute()->fetchFirstColumn();
    }

    private function buildObject(array $location): Location
    {
        return new Location(
            $location['name'],
            $location['street'],
            $location['zip'],
            $location['city'],
            $location['district'],
            $location['country'],
            $location['phone'],
            $location['latitude'],
            $location['longitude'],
            (int)$location['sys_language_uid']
        );
    }

    private function updateLocation(Location $location, int $uid): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $queryBuilder->update('tx_events_domain_model_location');
        $queryBuilder->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid)));
        $queryBuilder->set('global_id', $location->getGlobalId());
        $queryBuilder->set('latitude', $location->getLatitude());
        $queryBuilder->set('longitude', $location->getLongitude());
        $queryBuilder->execute();
    }

    /**
     * @param int[] $uids
     */
    private function removeDuplicates(array $uids): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $queryBuilder->delete('tx_events_domain_model_location');
        $queryBuilder->where($queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY)));
        $queryBuilder->execute();
    }

    private function updateRelations(array $migration): void
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $queryBuilder->update('tx_events_domain_model_event');

        foreach ($migration as $legacyLocationUid => $newLocationUid) {
            $finalBuilder = clone $queryBuilder;
            $finalBuilder->where($finalBuilder->expr()->eq('location', $finalBuilder->createNamedParameter($legacyLocationUid)));
            $finalBuilder->set('location', $newLocationUid);
            $finalBuilder->execute();
        }
    }
}

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MigrateOldLocations implements UpgradeWizardInterface
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $uidsForTranslation = [];

    public function __construct(
        ConnectionPool $connectionPool,
        DataHandler $dataHandler,
        LogManager $logManager
    ) {
        $this->connectionPool = $connectionPool;
        $this->dataHandler = $dataHandler;
        $this->logger = $logManager->getLogger(self::class);
    }

    public function getTitle(): string
    {
        return 'Migrate EXT:event location data.';
    }

    public function getDescription(): string
    {
        return 'Checks for legacy location data stored within events and will create dedicated location records and relations.';
    }

    public function updateNecessary(): bool
    {
        return $this->hasOldColumns()
            && $this->getQueryBuilder()
            ->count('*')
            ->execute()
            ->fetchOne() > 0
        ;
    }

    public function executeUpdate(): bool
    {
        $result = $this->getQueryBuilder()->execute();
        foreach ($result as $eventRecord) {
            $this->logger->info('Updating event record.', ['record' => $eventRecord]);
            $eventRecord['location'] = $this->getLocationUid($eventRecord);
            $this->uidsForTranslation[$eventRecord['uid'] . '-' . $eventRecord['sys_language_uid']] = $eventRecord['location'];
            $this->updateEvent($eventRecord);
        }
        return true;
    }

    private function getLocationUid(array $event): int
    {
        $existingUid = $this->getExitingLocationUid($event);
        if ($existingUid > 0) {
            $this->logger->info('Location already exists', ['uid' => $existingUid, 'event' => $event]);
            return $existingUid;
        }

        return $this->createLocation($event);
    }

    private function getExitingLocationUid(array $event): int
    {
        $columns = [
            'sys_language_uid',
            'name',
            'street',
            'district',
            'city',
            'zip',
            'country',
            'phone',
            'latitude',
            'longitude',
        ];
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $qb->select('uid', 'l10n_parent');
        $qb->from('tx_events_domain_model_location');
        foreach ($columns as $column) {
            $qb->andWhere($qb->expr()->eq($column, $qb->createNamedParameter($event[$column])));
        }

        $uids = $qb->execute()->fetchAssociative();
        if (is_bool($uids)) {
            return 0;
        }

        return $uids['l10n_parent'] ?: $uids['uid'];
    }

    private function createLocation(array $event): int
    {
        $this->logger->info('Location will be created.', ['event' => $event]);

        $columnsToMap = [
            'pid',
            'sys_language_uid',
            'name',
            'street',
            'district',
            'city',
            'zip',
            'country',
            'phone',
            'latitude',
            'longitude',
        ];
        $record = [];

        foreach ($columnsToMap as $columnName) {
            $record[$columnName] = $event[$columnName];
        }
        $recordUid = 'NEW12121';
        $l10nParentUid = $this->uidsForTranslation[$event['l10n_parent'] . '-0'] ?? 0;
        $dataHandler = clone $this->dataHandler;

        if ($event['sys_language_uid'] > 0 && $l10nParentUid > 0) {
            $this->logger->info('Foreign language, create translation.', [
                'l10nParentUid' => $l10nParentUid,
                'event' => $event,
            ]);

            $dataHandler->start([], [
                'tx_events_domain_model_location' => [
                    $l10nParentUid => [
                        'localize' => $event['sys_language_uid'],
                    ],
                ],
            ]);
            $dataHandler->process_cmdmap();
            $recordUid = $dataHandler->copyMappingArray_merged['tx_events_domain_model_location'][$l10nParentUid] ?? 0;
        }

        $this->logger->info('Create or update loation.', [
            'recordUid' => $recordUid,
            'l10nParentUid' => $l10nParentUid,
            'event' => $event,
            'record' => $record,
        ]);

        $dataHandler->start([
            'tx_events_domain_model_location' => [
                $recordUid => $record,
            ],
        ], []);
        $dataHandler->process_datamap();

        $uid = $dataHandler->substNEWwithIDs[$recordUid] ?? 0;
        $this->logger->info('Created or updated location.', [
            'uid' => $uid,
        ]);
        if ($uid > 0) {
            return $uid;
        }
        if ($l10nParentUid > 0) {
            return $l10nParentUid;
        }

        throw new \Exception('Could not create location: ' . implode(', ', $dataHandler->errorLog), 1672916613);
    }

    private function updateEvent(array $event): void
    {
        $this->connectionPool
            ->getConnectionForTable('tx_events_domain_model_event')
            ->update(
                'tx_events_domain_model_event',
                ['location' => $event['location']],
                ['uid' => $event['uid']]
            )
        ;
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $columns = $this->columnsToFetch();

        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $qb->getRestrictions()->removeAll();
        $qb->select(...$columns);
        $qb->addSelect('uid', 'pid', 'sys_language_uid', 'l10n_parent');
        $qb->from('tx_events_domain_model_event');
        foreach ($columns as $columnName) {
            $qb->orWhere($qb->expr()->neq($columnName, $qb->createNamedParameter('')));
        }
        $qb->orderBy('sys_language_uid', 'ASC');
        $qb->addOrderBy('l10n_parent', 'ASC');
        $qb->addOrderBy('uid', 'ASC');

        return $qb;
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    private function hasOldColumns(): bool
    {
        $schema = $this->connectionPool
            ->getConnectionForTable('tx_events_domain_model_event')
            ->getSchemaManager()
            ->createSchema()
            ->getTable('tx_events_domain_model_event');

        foreach ($this->columnsToFetch() as $column) {
            if ($schema->hasColumn($column) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]
     */
    private function columnsToFetch(): array
    {
        return [
            'name',
            'street',
            'district',
            'city',
            'zip',
            'country',
            'phone',
            'latitude',
            'longitude',
        ];
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][self::class] = self::class;
    }
}

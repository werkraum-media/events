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
        return $this->getQueryBuilder()
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
        $oldColumns = [
            'sys_language_uid' => 'sys_language_uid',
            'zzz_deleted_name' => 'name',
            'zzz_deleted_street' => 'street',
            'zzz_deleted_district' => 'district',
            'zzz_deleted_city' => 'city',
            'zzz_deleted_zip' => 'zip',
            'zzz_deleted_country' => 'country',
            'zzz_deleted_phone' => 'phone',
            'zzz_deleted_latitude' => 'latitude',
            'zzz_deleted_longitude' => 'longitude',
        ];
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $qb->select('uid', 'l10n_parent');
        $qb->from('tx_events_domain_model_location');
        foreach ($oldColumns as $oldName => $newName) {
            $qb->andWhere($qb->expr()->eq($newName, $qb->createNamedParameter($event[$oldName])));
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

        $record = [
            'pid' => $event['pid'],
            'sys_language_uid' => $event['sys_language_uid'],
            'name' => $event['zzz_deleted_name'],
            'street' => $event['zzz_deleted_street'],
            'district' => $event['zzz_deleted_district'],
            'city' => $event['zzz_deleted_city'],
            'zip' => $event['zzz_deleted_zip'],
            'country' => $event['zzz_deleted_country'],
            'phone' => $event['zzz_deleted_phone'],
            'latitude' => $event['zzz_deleted_latitude'],
            'longitude' => $event['zzz_deleted_longitude'],
        ];
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
        $oldColumns = [
            'zzz_deleted_name',
            'zzz_deleted_street',
            'zzz_deleted_district',
            'zzz_deleted_city',
            'zzz_deleted_zip',
            'zzz_deleted_country',
            'zzz_deleted_phone',
            'zzz_deleted_latitude',
            'zzz_deleted_longitude',
        ];
        $qb = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_event');
        $qb->getRestrictions()->removeAll();
        $qb->select(...$oldColumns);
        $qb->addSelect('uid', 'pid', 'sys_language_uid', 'l10n_parent');
        $qb->from('tx_events_domain_model_event');
        foreach ($oldColumns as $columnName) {
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

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][self::class] = self::class;
    }
}

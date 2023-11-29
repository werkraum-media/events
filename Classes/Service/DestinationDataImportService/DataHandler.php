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

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Events\Service\DestinationDataImportService\DataHandler\Assignment;

final class DataHandler
{
    private readonly LoggerInterface $logger;

    public function __construct(
        LogManager $logManager
    ) {
        $this->logger = $logManager->getLogger(self::class);
    }

    /**
     * @param Assignment[] $assignments
     */
    public function updateEvent(
        int $eventUid,
        array $assignments
    ): void {
        $data = ['tx_events_domain_model_event' => [$eventUid => []]];
        foreach ($assignments as $assignment) {
            $data['tx_events_domain_model_event'][$eventUid][$assignment->getColumnName()] = $assignment->getValue();
        }

        $this->logger->debug('Update event data.', $data);
        $dataHandler = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            $this->logger->error('Error during update of event data.', [
                'assignments' => $assignments,
                'errors' => $dataHandler->errorLog,
            ]);
        }
    }
}

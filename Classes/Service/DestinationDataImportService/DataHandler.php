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

namespace Wrm\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\DataHandling\DataHandler as Typo3DataHandler;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Service\DestinationDataImportService\DataHandler\Assignment;

final class DataHandler
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        LogManager $logManager
    ) {
        $this->logger = $logManager->getLogger(__CLASS__);
    }

    public function storeAssignments(
        Assignment $assignment
    ): void {
        $data = [
            'tx_events_domain_model_event' => [
                $assignment->getUid() => [
                    $assignment->getColumnName() => implode(',', $assignment->getUids()),
                ],
            ],
        ];

        $this->logger->debug('Import assignment.', $data);
        $dataHandler = GeneralUtility::makeInstance(Typo3DataHandler::class);
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            $this->logger->error('Error during import of assignments.', [
                'assignment' => $assignment,
                'errors' => $dataHandler->errorLog,
            ]);
        }
    }
}

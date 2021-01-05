<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Service\Cleanup\Database;
use Wrm\Events\Service\Cleanup\Files;

class CleanupService
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var Files
     */
    private $files;

    public function __construct(Database $database, Files $files)
    {
        $this->database = $database;
        $this->files = $files;
    }

    public function deleteAllData()
    {
        $this->database->truncateTables(... [Database::DATE_TABLE, Database::ORGANIZER_TABLE]);
        $this->removeViaDataHandler($this->database->getDeletionStructureForEvents());
        $this->files->deleteAll();
    }

    public function deletePastData()
    {
        $this->database->deleteDates(... $this->database->getPastDates());
        $this->removeViaDataHandler($this->database->getDeletionStructureForEventsWithoutDates());
        $this->files->deleteDangling();
    }

    private function removeViaDataHandler(array $structure)
    {
        /* @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $structure);
        $dataHandler->process_cmdmap();
    }
}

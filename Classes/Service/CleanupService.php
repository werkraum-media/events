<?php

namespace Wrm\Events\Service;

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

    public function deleteAllData(): void
    {
        $this->database->truncateTables();
        $this->files->deleteDangling();
    }

    public function deletePastData(): void
    {
        $this->database->deleteDates(...$this->database->getPastDates());
        $this->database->deleteEventsWithoutDates();
        $this->files->deleteDangling();
    }
}

<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service;

use WerkraumMedia\Events\Service\Cleanup\Database;
use WerkraumMedia\Events\Service\Cleanup\Files;

final class CleanupService
{
    public function __construct(
        private readonly Database $database,
        private readonly Files $files
    ) {
    }

    public function deleteAllData(): void
    {
        $this->database->truncateTables();
        $this->files->deleteDangling();
    }

    public function deletePastData(): void
    {
        $this->database->deletePastDates();
        $this->database->deleteEventsWithoutDates();
        $this->files->deleteDangling();
    }
}

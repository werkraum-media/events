<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use WerkraumMedia\Events\Service\CleanupService;

class RemoveAllCommand extends Command
{
    public function __construct(
        private readonly CleanupService $cleanupService
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Remove all event data');
        $this->setHelp('All events and associated data will be removed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Bootstrap::initializeBackendAuthentication();
        $this->cleanupService->deleteAllData();

        return 0;
    }
}

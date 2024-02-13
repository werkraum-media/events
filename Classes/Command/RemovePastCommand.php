<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use WerkraumMedia\Events\Service\CleanupService;

class RemovePastCommand extends Command
{
    public function __construct(
        private readonly CleanupService $cleanupService
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Remove past events');
        $this->setHelp('Past dates are removed, together with events that do not have any left dates.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Bootstrap::initializeBackendAuthentication();

        $this->cleanupService->deletePastData();
        return 0;
    }
}

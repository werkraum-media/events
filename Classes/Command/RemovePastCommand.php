<?php

namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Wrm\Events\Service\CleanupService;

class RemovePastCommand extends Command
{
    public function configure()
    {
        $this->setDescription('Remove past events');
        $this->setHelp('Past dates are removed, together with events that do not have any left dates.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(CleanupService::class)
            ->deletePastData();
    }
}

<?php
namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Extbase\Object\ObjectManager;

use Wrm\Events\Service\CleanupService;

class CleanupCommand extends Command {

    public function configure()
    {
        $this->setDescription('Cleanup Events');
        $this->setHelp('Events are cleaned up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(CleanupService::class)
            ->doClean();
    }
}
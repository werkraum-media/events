<?php
namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Bootstrap;

class CleanupCommand extends Command {

    protected $restExperience;
    protected $storagePid;
    protected $regionUid;
    protected $categoryParentUid;
    protected $filesFolder;

    protected $cliOutput;
    protected $cliInput;

    protected $cleanupService;

    public function configure()
    {
        $this->setDescription('Cleanup Events');
        $this->setHelp('Events are cleaned up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cliOutput = $output;
        $this->cliInput = $input;

        Bootstrap::initializeBackendAuthentication();

        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->destinationDataImportService = $this->objectManager->get('Wrm\\Events\\Service\\CleanupService');

        return $this->cleanupService->doClean();
    }
}
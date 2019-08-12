<?php
namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Bootstrap;

class DestinationDataImportCommand extends Command {

    protected $restExperience;
    protected $storagePid;
    protected $regionUid;
    protected $categoryParentUid;
    protected $filesFolder;

    protected $cliOutput;
    protected $cliInput;

    protected $destinationDataImportService;

    public function configure()
    {
        $this->setDescription('Import Destination Data Events');
        $this->setHelp('Destination Data Events are imported');

        $this->addArgument(
            'storage-pid',
            InputArgument::OPTIONAL,
            'What is the storage pid?',
            '281'
        );
        $this->addArgument(
            'region-uid',
            InputArgument::OPTIONAL,
            'What is the region uid?',
            '3'
        );
        $this->addArgument(
            'category-parent-uid',
            InputArgument::OPTIONAL,
            'What is the default category parent uid?',
            '6'
        );
        $this->addArgument('rest-experience',
            InputArgument::OPTIONAL,
            'What is the rest experience?',
            'arnstadt'
        );
        $this->addArgument('files-folder',
            InputArgument::OPTIONAL,
            'Where to save the image files?',
            'redaktion/arnstadt/events/'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cliOutput = $output;
        $this->cliInput = $input;

        $this->storagePid = $input->getArgument('storage-pid');
        $this->regionUid = $input->getArgument('region-uid');
        $this->categoryParentUid = $input->getArgument('category-parent-uid');
        $this->filesFolder = $input->getArgument('files-folder');
        $this->restExperience = $input->getArgument('rest-experience');

        Bootstrap::initializeBackendAuthentication();

        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->destinationDataImportService = $this->objectManager->get('Wrm\\Events\\Service\\DestinationDataImportService');

        return $this->destinationDataImportService->import(
            $this->restExperience,
            $this->storagePid,
            $this->regionUid,
            $this->categoryParentUid,
            $this->filesFolder
        );
    }
}
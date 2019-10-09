<?php
namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Extbase\Object\ObjectManager;

use Wrm\Events\Service\DestinationDataImportService;

class DestinationDataImportCommand extends Command {

    public function configure()
    {
        $this->setDescription('Import Destination Data Events');
        $this->setHelp('Destination Data Events are imported');

        $this->addArgument(
            'storage-pid',
            InputArgument::OPTIONAL,
            'What is the storage pid?',
            '6'
        );
        $this->addArgument(
            'region-uid',
            InputArgument::OPTIONAL,
            'What is the region uid?',
            '1'
        );
        $this->addArgument(
            'category-parent-uid',
            InputArgument::OPTIONAL,
            'What is the default category parent uid?',
            '52'
        );
        $this->addArgument('rest-experience',
            InputArgument::OPTIONAL,
            'What is the rest experience?',
            'stadtmarketing-erfurt'
        );
        $this->addArgument('files-folder',
            InputArgument::OPTIONAL,
            'Where to save the image files?',
            'staedte/erfurt/events/'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(DestinationDataImportService::class)
            ->import(
                $input->getArgument('rest-experience'),
                $input->getArgument('storage-pid'),
                $input->getArgument('region-uid'),
                $input->getArgument('category-parent-uid'),
                $input->getArgument('files-folder')
            );
    }
}
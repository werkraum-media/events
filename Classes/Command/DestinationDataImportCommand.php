<?php

namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use Wrm\Events\Domain\DestinationData\Import;
use Wrm\Events\Service\DestinationDataImportService;

class DestinationDataImportCommand extends Command
{
    /**
     * @var DestinationDataImportService
     */
    private $destinationDataImportService;

    public function __construct(
        DestinationDataImportService $destinationDataImportService
    ) {
        parent::__construct();
        $this->destinationDataImportService = $destinationDataImportService;
    }

    public function configure(): void
    {
        $this->setDescription('Import Destination Data Events');
        $this->setHelp('Destination Data Events are imported');

        $this->addArgument(
            'storage-pid',
            InputArgument::REQUIRED,
            'What is the storage pid?'
        );
        $this->addArgument(
            'rest-experience',
            InputArgument::REQUIRED,
            'What is the rest experience?'
        );
        $this->addArgument(
            'files-folder',
            InputArgument::REQUIRED,
            'Where to save the image files?'
        );
        $this->addArgument(
            'region-uid',
            InputArgument::OPTIONAL,
            'What is the region uid?'
        );
        $this->addArgument(
            'query',
            InputArgument::OPTIONAL,
            'What is the additional query "q" parameter?'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        $regionUid = $input->getArgument('region-uid');
        if (is_numeric($regionUid)) {
            $regionUid = (int) $regionUid;
        } else {
            $regionUid = null;
        }

        $query = $input->getArgument('query');
        if (is_string($query) === false) {
            $query = '';
        }

        return $this->destinationDataImportService->import(new Import(
            $input->getArgument('rest-experience'),
            $input->getArgument('storage-pid'),
            $regionUid,
            $input->getArgument('files-folder'),
            $query
        ));
    }
}

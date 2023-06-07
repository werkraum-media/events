<?php

namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use Wrm\Events\Domain\DestinationData\ImportFactory;
use Wrm\Events\Service\DestinationDataImportService;

class ImportDestinationDataViaAllConfigruationsCommand extends Command
{
    /**
     * @var DestinationDataImportService
     */
    private $destinationDataImportService;

    /**
     * @var ImportFactory
     */
    private $importFactory;

    public function __construct(
        DestinationDataImportService $destinationDataImportService,
        ImportFactory $importFactory
    ) {
        parent::__construct();
        $this->destinationDataImportService = $destinationDataImportService;
        $this->importFactory = $importFactory;
    }

    public function configure(): void
    {
        $this->setDescription('Import Destination Data Events');
        $this->setHelp('Destination Data Events are imported from all configuration records.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        $finalResult = 0;
        foreach ($this->importFactory->createAll() as $import) {
            $result = $this->destinationDataImportService->import($import);
            if ($result !== 0) {
                $finalResult = $result;
            }
        }

        return $finalResult;
    }
}

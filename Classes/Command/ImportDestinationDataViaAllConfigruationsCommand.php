<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use WerkraumMedia\Events\Domain\DestinationData\ImportFactory;
use WerkraumMedia\Events\Service\DestinationDataImportService;

class ImportDestinationDataViaAllConfigruationsCommand extends Command
{
    public function __construct(
        private readonly DestinationDataImportService $destinationDataImportService,
        private readonly ImportFactory $importFactory
    ) {
        parent::__construct();
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

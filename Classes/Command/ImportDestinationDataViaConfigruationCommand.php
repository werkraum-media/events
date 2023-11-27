<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use WerkraumMedia\Events\Domain\DestinationData\ImportFactory;
use WerkraumMedia\Events\Service\DestinationDataImportService;

class ImportDestinationDataViaConfigruationCommand extends Command
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
        $this->setHelp('Destination Data Events are imported from given configuration record.');

        $this->addArgument(
            'configurationUid',
            InputArgument::REQUIRED,
            'UID of the configuration to import'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Bootstrap::initializeBackendAuthentication();

        $configurationUid = $input->getArgument('configurationUid');
        if (is_numeric($configurationUid)) {
            $configurationUid = (int)$configurationUid;
        } else {
            throw new Exception('No numeric uid for configuration provided.', 1643267138);
        }

        $import = $this->importFactory->createFromUid(
            $configurationUid
        );
        return $this->destinationDataImportService->import(
            $import
        );
    }
}

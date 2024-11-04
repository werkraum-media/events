<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[TestDox('DestinationData import')]
class ImportsWithoutRegionIfNotProvidedTest extends AbstractTestCase
{
    #[Test]
    public function importsWithoutRegionIfNotProvided(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithoutRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_partner'),
            'Added unexpected partners.'
        );
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_region'),
            'Added unexpected region.'
        );
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithoutRegionIfNotProvided.php');

        $importedFiles = GeneralUtility::getFilesInDir($this->fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'lutherkirche-jpg.jpg',
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );

        $this->assertEmptyLog();
    }
}

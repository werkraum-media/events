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
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://example.com/some-path/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

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

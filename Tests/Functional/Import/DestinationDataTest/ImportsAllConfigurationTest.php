<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Command\ImportDestinationDataViaAllConfigruationsCommand;

/**
 * @testdox Import of configuration
 */
class ImportsAllConfigurationTest extends AbstractTest
{
    /**
     * @test
     */
    public function importsConfiguration(): void
    {
        $this->setDateAspect(new \DateTimeImmutable('2021-07-13', new \DateTimeZone('Europe/Berlin')));

        $fileImportPathConfiguration1 = 'staedte/beispielstadt/events/';
        $fileImportPath1 = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration1;
        GeneralUtility::mkdir_deep($fileImportPath1);

        $fileImportPathConfiguration2 = 'staedte/anderestadt/events/';
        $fileImportPath2 = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration2;
        GeneralUtility::mkdir_deep($fileImportPath2);

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SecondImportConfiguration.php');
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restTemplate = ET2014A.json',
            'restLimit = 3',
            'restMode = next_months,12',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),

            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand([], ImportDestinationDataViaAllConfigruationsCommand::class);

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(8, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://example.com/some-path/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json&q=name%3A%22Beispiel%22', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

        self::assertSame('https://example.com/some-path/?experience=anderestadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json&q=name%3A%22Beispiel2%22', (string)$requests[4]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[5]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[6]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[7]['request']->getUri());

        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_partner'),
            'Added unexpected partners.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_region'),
            'Added or removed unexpected region.'
        );
        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsAllConfigurationTest.csv');

        $importedFiles = GeneralUtility::getFilesInDir($fileImportPath1);
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

        $importedFiles = GeneralUtility::getFilesInDir($fileImportPath2);
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

        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

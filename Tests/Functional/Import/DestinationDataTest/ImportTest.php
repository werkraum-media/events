<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Events\Command\ImportDestinationDataViaAllConfigruationsCommand;
use WerkraumMedia\Events\Service\DestinationDataImportService\Events\EventImportEvent;

#[TestDox('DestinationData import')]
final class ImportTest extends AbstractTestCase
{
    #[Test]
    public function cleansTransientFiles(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

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

        $transientFiles = GeneralUtility::getFilesInDir(Environment::getVarPath() . '/transient/');
        self::assertIsArray($transientFiles, 'Failed to retrieve transient files from filesystem.');
        self::assertCount(0, $transientFiles, 'Got unexpected number of files');

        $this->assertEmptyLog();
    }

    #[Test]
    public function doesNotUseUploadsFolder(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();
        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

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

        self::assertFalse(file_exists(Environment::getPublicPath() . '/uploads/tx_events/'), 'Uploads folder exists.');

        $this->assertEmptyLog();
    }

    /**
     * Files with different file extension than mime type will trigger a TYPO3 exception.
     *
     * We therefore try to fix the file extension to still import the file.
     */
    #[Test]
    public function importFixesFileExtensionToMatchMimeType(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithBrokenResourceConsistencyCheck.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/PngWithJpgFileExtension.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportFixesFileExtensionToMatchMimeType.php');
        $this->assertEmptyLog();
    }

    /**
     * The file has a different file extension than mime type and matching extension is not supported image extension.
     *
     * We expect the event to be imported, just the file should be missing.
     */
    #[Test]
    public function importDoesntBreakDueToResourceConsistencyCheck(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithBrokenResourceConsistencyCheck.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResourceConsistencyFailingImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportDoesntBreakWithBrokenImageFileName.php');
        $this->assertMessageInLog('component="WerkraumMedia.Events.Service.DestinationDataImportService.FilesAssignment": Could not fix filename based on mime type.');
    }

    #[Test]
    public function importDoesntBreakWithLongFileTitle(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithCategories.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithVeryLongFileName.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportDoesntBreakWithLongFileTitle.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function importDoesntEndUpInEndlessDateCreation(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2022-07-01'));
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithPotentiellyEndlessDateCreation.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportDoesntEndUpInEndlessDateCreation.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function importsAllConfigurationTest(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2021-07-13', new DateTimeZone('Europe/Berlin')));

        $fileImportPathConfiguration2 = 'staedte/anderestadt/events/';
        $fileImportPath2 = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration2;
        GeneralUtility::mkdir_deep($fileImportPath2);

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SecondImportConfiguration.php');

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
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&q=name%3A%22Beispiel%22&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=anderestadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&q=name%3A%22Beispiel2%22&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[4]['request']->getUri());
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
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsAllConfiguration.php');

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

        $this->assertEmptyLog();
    }

    #[Test]
    public function importsExampleAsExpected(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2021-07-13', new DateTimeZone('Europe/Berlin')));

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithCategories.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

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
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsExampleAsExpected.php');

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

    #[Test]
    public function importsSource(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSources.json') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsSource.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function importsKeywords(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithKeywords.json') ?: ''),
        ]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsKeywords.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function addsNewFeatures(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFeaturesAddsNewFeatures.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function addsNewFeaturesToExistingOnes(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ExistingFeatures.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFeaturesAddsNewFeatures.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function importsConfiguration(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2021-07-13', new DateTimeZone('Europe/Berlin')));

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&q=name%3A%22Beispiel%22&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

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
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsExampleAsExpected.php');

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

    /**
     * @todo: Missing "ticket" example and combinations.
     *        Could not find any "ticket" real world example.
     */
    #[Test]
    public function importsTickets(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithTickets.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(1, $requests, 'Unexpected number of requests were made.');
        self::assertSame('http://meta.et4.de/rest.ashx/search/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc', (string)$requests[0]['request']->getUri());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsTickets.php');

        $this->assertEmptyLog();
    }

    #[Test]
    public function importsWithoutCategoryIfNotProvided(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');

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
            1,
            $this->getAllRecords('tx_events_domain_model_region'),
            'Added or removed unexpected region.'
        );
        self::assertCount(
            0,
            $this->getAllRecords('sys_category'),
            'Added unexpected category.'
        );
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithoutCategoryIfNotProvided.php');

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

    #[Test]
    public function importsWithoutLocationIfNotProvided(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithoutRegion.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithoutLocation.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_location'),
            'Added unexpected location.'
        );
        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsWithoutLocationIfNotProvided.php');
        $this->assertEmptyLog();
    }

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

    #[Test]
    public function handlesExceptionDuringEventImport(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfigurationWithoutRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');

        $this->addEventListener(EventImportEvent::class, function (EventImportEvent $event): void {
            throw new Exception('Exception from event handler within event.', 1768210296);
        });

        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(1, $tester->getStatusCode());

        $this->assertDoesNotHaveRecordsInTable('tx_events_domain_model_partner');
        $this->assertDoesNotHaveRecordsInTable('tx_events_domain_model_location');
        $this->assertDoesNotHaveRecordsInTable('tx_events_domain_model_event');
        $this->assertDoesNotHaveRecordsInTable('tx_events_domain_model_date');

        $this->assertMessageInLog(
            'Error happened while importing event "Allerlei Weihnachtliches (Heute mit Johannes Geißer)" with global id: e_100347853, got error: Exception from event handler within event.',
        );
        $this->assertMessageInLog(
            'Error happened while importing event "Tüftlerzeit" with global id: e_100354481, got error: Exception from event handler within event.',
        );
        $this->assertMessageInLog(
            'Error happened while importing event "Adventliche Orgelmusik (Orgel: KMD Frank Bettenhausen)" with global id: e_100350503, got error: Exception from event handler within event.',
        );
    }

    #[Test]
    public function importsAssociatesEventWithImport(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2021-07-13', new DateTimeZone('Europe/Berlin')));

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleCategory.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleImportConfiguration.php');

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);
        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsAssociatesEventWithImport.php');

        $this->assertEmptyLog();
    }
}

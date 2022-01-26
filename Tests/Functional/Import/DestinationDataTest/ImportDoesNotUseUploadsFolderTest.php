<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @testdox DestinationData import
 */
class ImportDoesNotUseUploadsFolderTest extends AbstractTest
{
    /**
     * @test
     */
    public function doesNotUseUploadsFolder(): void
    {
        $fileImportPath = 'staedte/beispielstadt/events/';

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
            'categoriesPid = ',
            'categoryParentUid = ',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Response.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ]);

        $tester = $this->executeCommand([
            'storage-pid' => '2',
            'rest-experience' => 'beispielstadt',
            'files-folder' => $fileImportPath,
        ]);
        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(4, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://example.com/some-path/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[2]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[3]['request']->getUri());

        $importedFiles = GeneralUtility::getFilesInDir($this->getInstancePath() . '/fileadmin/' . $fileImportPath);
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

        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

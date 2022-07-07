<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @testdox DestinationData import
 */
class ImportsTicketsTest extends AbstractTest
{
    /**
     * @test
     * @todo: Missing "ticket" example and combinations.
     *        Could not find any "ticket" real world example.
     */
    public function importsExampleAsExpected(): void
    {
        $fileImportPathConfiguration = 'staedte/beispielstadt/events/';
        $fileImportPath = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration;
        GeneralUtility::mkdir_deep($fileImportPath);

        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/SingleRegion.xml');
        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/SingleCategory.xml');
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
            'categoriesPid = 2',
            'categoryParentUid = 2',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithTickets.json') ?: ''),
        ]);

        $tester = $this->executeCommand([
            'storage-pid' => '2',
            'rest-experience' => 'beispielstadt',
            'files-folder' => $fileImportPathConfiguration,
            'region-uid' => '1',
        ]);

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(1, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://example.com/some-path/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json', (string)$requests[0]['request']->getUri());

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsTickets.csv');

        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

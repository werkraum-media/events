<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @testdox DestinationData import
 */
class ImportsWithLocationsTest extends AbstractTest
{
    /**
     * @test
     */
    public function importsWithLocations(): void
    {
        $fileImportPathConfiguration = 'staedte/beispielstadt/events/';
        $fileImportPath = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration;
        GeneralUtility::mkdir_deep($fileImportPath);

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);

        $requests = &$this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithLocations.json') ?: ''),
        ]);
        $tester = $this->executeCommand([
            'storage-pid' => '2',
            'rest-experience' => 'beispielstadt',
            'files-folder' => $fileImportPathConfiguration,
            'region-uid' => '',
        ]);

        self::assertSame(0, $tester->getStatusCode());
        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsWithLocations.csv');
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

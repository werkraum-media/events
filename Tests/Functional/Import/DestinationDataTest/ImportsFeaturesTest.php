<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;

/**
 * @testdox DestinationData import
 */
class ImportsFeaturesTest extends AbstractTest
{
    /**
     * @test
     * Only 1 associated feature count as other features are new and hidden and not counted.
     */
    public function addsNewFeatures(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFeaturesAddsNewFeatures.csv');
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

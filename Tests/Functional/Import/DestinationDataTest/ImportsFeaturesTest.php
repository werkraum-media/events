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
        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function addsNewFeaturesToExistingOnes(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FeaturesImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/ExistingFeatures.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithFeatures.json') ?: ''),
        ]);
        $tester = $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFeaturesAddsNewFeatures.csv');
        $this->assertEmptyLog();
    }
}

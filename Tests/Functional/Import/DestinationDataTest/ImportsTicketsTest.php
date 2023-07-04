<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;

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
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/DefaultImportConfiguration.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SingleRegion.php');
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
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithTickets.json') ?: ''),
        ]);

        $tester = $this->executeCommand();

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(1, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://example.com/some-path/?experience=beispielstadt&licensekey=example-license&type=Event&mode=next_months%2C12&limit=3&template=ET2014A.json', (string)$requests[0]['request']->getUri());

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsTickets.csv');

        $this->assertEmptyLog();
    }
}

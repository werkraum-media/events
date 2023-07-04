<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;

/**
 * @testdox DestinationData import
 */
class ImportsFirstDateOfDatesTest extends AbstractTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FirstDateOfRecurringDatesImportConfiguration.php');
        $this->setDateAspect(new \DateTimeImmutable('2022-07-13', new \DateTimeZone('UTC')));
    }

    /**
     * @test
     */
    public function singelDate(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSingleDate.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFirstDateOfSingleDate.csv');
        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function recurringWeekly(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeekly.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFirstDateOfRecurringDatesWeekly.csv');
        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function recurringDaily(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDaily.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFirstDateOfRecurringDatesDaily.csv');
        $this->assertEmptyLog();
    }
}

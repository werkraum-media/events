<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;

/**
 * @testdox DestinationData import
 */
class ImportsWithConfiguredRepeatUntilTest extends AbstractTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/MinimalImportConfiguration.php');
        $this->setDateAspect(new \DateTimeImmutable('2022-07-13', new \DateTimeZone('UTC')));
    }

    /**
     * @test
     */
    public function recurringWeekly(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ], [
            'repeatUntil = +30 days',
        ]);
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeeklyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsRecurringDatesWeeklyWithConfiguredRepeatUntil.csv');
        $this->assertEmptyLog();
    }

    /**
     * @test
     */
    public function recurringDaily(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ], [
            'repeatUntil = +10 days',
        ]);
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDailyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsRecurringDatesDailyWithConfiguredRepeatUntil.csv');
        $this->assertEmptyLog();
    }
}

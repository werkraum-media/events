<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\Psr7\Response;
use Wrm\Events\Command\ImportDestinationDataViaConfigruationCommand;

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
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }

    /**
     * @test
     */
    public function recurringWeekly(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeekly.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFirstDateOfRecurringDatesWeekly.csv');
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }

    /**
     * @test
     */
    public function recurringDaily(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDaily.json') ?: '')]);

        $this->executeCommand();

        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsFirstDateOfRecurringDatesDaily.csv');
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log',
            'Logfile was not empty.'
        );
    }
}

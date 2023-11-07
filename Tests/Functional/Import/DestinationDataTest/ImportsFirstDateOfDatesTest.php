<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
class ImportsFirstDateOfDatesTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ]);
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FirstDateOfRecurringDatesImportConfiguration.php');
        $this->setDateAspect(new DateTimeImmutable('2022-07-13', new DateTimeZone('UTC')));
    }

    #[Test]
    public function singelDate(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithSingleDate.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFirstDateOfSingleDate.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringWeekly(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeekly.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFirstDateOfRecurringDatesWeekly.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringDaily(): void
    {
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDaily.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsFirstDateOfRecurringDatesDaily.php');
        $this->assertEmptyLog();
    }
}

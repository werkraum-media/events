<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('DestinationData import')]
class ImportsWithConfiguredRepeatUntilTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/MinimalImportConfiguration.php');
        $this->setDateAspect(new DateTimeImmutable('2022-07-13', new DateTimeZone('UTC')));
    }

    #[Test]
    public function recurringWeekly(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ], [
            'repeatUntil = +30 days',
        ]);
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeeklyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesWeeklyWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringDaily(): void
    {
        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
        ], [
            'repeatUntil = +10 days',
        ]);
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDailyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesDailyWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }
}

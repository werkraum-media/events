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
    public function recurringMonthlyFirstWeekday(): void
    {
        $this->getConnectionPool()
            ->getConnectionForTable('tx_events_domain_model_import')
            ->update(
                'tx_events_domain_model_import',
                ['import_repeat_until' => '+90 days'],
                ['uid' => '1']
            )
        ;
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringMonthlyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesMonthlyWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringMonthlySecondWeekday(): void
    {
        $this->getConnectionPool()
            ->getConnectionForTable('tx_events_domain_model_import')
            ->update(
                'tx_events_domain_model_import',
                ['import_repeat_until' => '+100 days'],
                ['uid' => '1']
            )
        ;
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringMonthlySecondWeekdayWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesMonthlySecondWeekdayWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringWeekly(): void
    {
        $this->getConnectionPool()
            ->getConnectionForTable('tx_events_domain_model_import')
            ->update(
                'tx_events_domain_model_import',
                ['import_repeat_until' => '+30 days'],
                ['uid' => '1']
            )
        ;
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringWeeklyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesWeeklyWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }

    #[Test]
    public function recurringDaily(): void
    {
        $this->getConnectionPool()
            ->getConnectionForTable('tx_events_domain_model_import')
            ->update(
                'tx_events_domain_model_import',
                ['import_repeat_until' => '+10 days'],
                ['uid' => '1']
            )
        ;
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ResponseWithRecurringDailyWithoutRepeatUntil.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/ImportsRecurringDatesDailyWithConfiguredRepeatUntil.php');
        $this->assertEmptyLog();
    }
}

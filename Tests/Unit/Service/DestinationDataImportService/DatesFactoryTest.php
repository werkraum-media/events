<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Service\DestinationDataImportService\DatesFactory;

/**
 * @covers \Wrm\Events\Service\DestinationDataImportService\DatesFactory
 */
class DatesFactoryTest extends TestCase
{
    private function createTestSubject(
        string $contextDate
    ): DatesFactory {
        $logger = $this->createStub(Logger::class);
        $logManager = $this->createStub(LogManager::class);
        $logManager->method('getLogger')->willReturn($logger);

        return new DatesFactory(
            $this->createContext(new \DateTimeImmutable($contextDate)),
            $this->createStub(ConfigurationManager::class),
            $logManager
        );
    }
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = $this->createTestSubject('now');

        self::assertInstanceOf(
            DatesFactory::class,
            $subject
        );
    }

    /**
     * @test
     *
     * @dataProvider possibleUnkownInput
     */
    public function returnsNoResultOnUnkownInput(array $unkownInput): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates($unkownInput, false);

        self::assertInstanceOf(\Generator::class, $result);
        self::assertCount(0, iterator_to_array($result));
    }

    public function possibleUnkownInput(): array
    {
        return [
            'Empty Intervals' => [
                'unkownInput' => [],
            ],
            'Single interval without values' => [
                'unkownInput' => [
                    [
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function returnsSingleNotCanceledDate(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-04-01T16:00:00+02:00',
            'end' => '2022-04-01T17:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);

        $firstEntry = $result->current();

        self::assertCount(1, iterator_to_array($result));

        self::assertInstanceOf(Date::class, $firstEntry);
        self::assertSame(1648821600, $firstEntry->getStart()->getTimestamp());
        self::assertSame(1648825200, $firstEntry->getEnd()->getTimestamp());
        self::assertSame('no', $firstEntry->getCanceled());
    }

    /**
     * @test
     */
    public function returnsWeeklyWithConfiguredRepeat(): void
    {
        $subject = $this->createTestSubject('2023-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'weekdays' => [
                'Monday',
                'Friday',
            ],
            'start' => '2023-01-06T14:00:00+01:00',
            'end' => '2023-01-06T15:00:00+01:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Weekly',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(16, $result);
    }

    /**
     * @test
     */
    public function returnsSingleCanceledDate(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-04-01T16:00:00+02:00',
            'end' => '2022-04-01T17:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);

        $firstEntry = $result->current();

        self::assertCount(1, iterator_to_array($result));

        self::assertInstanceOf(Date::class, $firstEntry);
        self::assertSame(1648821600, $firstEntry->getStart()->getTimestamp());
        self::assertSame(1648825200, $firstEntry->getEnd()->getTimestamp());
        self::assertSame('canceled', $firstEntry->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnDailyBasis(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-10-29T16:00:00+02:00',
            'end' => '2022-10-29T17:00:00+02:00',
            'repeatUntil' => '2022-11-02T17:00:00+01:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Daily',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(5, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame(1667052000, $result[0]->getStart()->getTimestamp());
        self::assertSame(1667055600, $result[0]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame(1667401200, $result[4]->getStart()->getTimestamp());
        self::assertSame(1667404800, $result[4]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[4]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsNotCanceledDatesOnDailyBasis(): void
    {
        $subject = $this->createTestSubject('2022-08-29T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-10-29T16:00:00+02:00',
            'end' => '2022-10-29T17:00:00+02:00',
            'repeatUntil' => '2022-11-02T17:00:00+01:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Daily',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(5, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame(1667052000, $result[0]->getStart()->getTimestamp());
        self::assertSame(1667055600, $result[0]->getEnd()->getTimestamp());
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame(1667401200, $result[4]->getStart()->getTimestamp());
        self::assertSame(1667404800, $result[4]->getEnd()->getTimestamp());
        self::assertSame('no', $result[4]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnWeeklyBasis(): void
    {
        $subject = $this->createTestSubject('2022-08-29T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'weekdays' => [
                'Saturday',
                'Sunday',
            ],
            'start' => '2022-10-29T16:00:00+02:00',
            'end' => '2022-10-29T17:00:00+02:00',
            'repeatUntil' => '2022-11-06T17:00:00+01:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Weekly',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame(1667052000, $result[0]->getStart()->getTimestamp());
        self::assertSame(1667055600, $result[0]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame(1667660400, $result[1]->getStart()->getTimestamp());
        self::assertSame(1667664000, $result[1]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[1]->getCanceled());

        self::assertSame(1667142000, $result[2]->getStart()->getTimestamp());
        self::assertSame(1667145600, $result[2]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[2]->getCanceled());

        self::assertSame(1667746800, $result[3]->getStart()->getTimestamp());
        self::assertSame(1667750400, $result[3]->getEnd()->getTimestamp());
        self::assertSame('canceled', $result[3]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsNotCanceledDatesOnWeeklyBasis(): void
    {
        $subject = $this->createTestSubject('2022-08-29T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'weekdays' => [
                'Saturday',
                'Sunday',
            ],
            'start' => '2022-10-29T16:00:00+02:00',
            'end' => '2022-10-29T17:00:00+02:00',
            'repeatUntil' => '2022-11-06T17:00:00+01:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Weekly',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame(1667052000, $result[0]->getStart()->getTimestamp());
        self::assertSame(1667055600, $result[0]->getEnd()->getTimestamp());
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame(1667660400, $result[1]->getStart()->getTimestamp());
        self::assertSame(1667664000, $result[1]->getEnd()->getTimestamp());
        self::assertSame('no', $result[1]->getCanceled());

        self::assertSame(1667142000, $result[2]->getStart()->getTimestamp());
        self::assertSame(1667145600, $result[2]->getEnd()->getTimestamp());
        self::assertSame('no', $result[2]->getCanceled());

        self::assertSame(1667746800, $result[3]->getStart()->getTimestamp());
        self::assertSame(1667750400, $result[3]->getEnd()->getTimestamp());
        self::assertSame('no', $result[3]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnMixedIntervals(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([
            [
                'start' => '2022-06-21T16:00:00+02:00',
                'end' => '2022-06-21T22:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'interval' => 1,
            ],
            [
                'start' => '2022-04-01T16:00:00+02:00',
                'end' => '2022-04-01T17:00:00+02:00',
                'repeatUntil' => '2022-04-03T18:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Daily',
                'interval' => 1,
            ],
            [
                'weekdays' => [
                    'Saturday',
                    'Sunday',
                ],
                'start' => '2022-03-02T11:00:00+01:00',
                'end' => '2022-03-02T13:00:00+01:00',
                'repeatUntil' => '2022-03-15T13:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Weekly',
                'interval' => 1,
            ],
        ], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(8, $result);

        foreach ($result as $date) {
            self::assertInstanceOf(Date::class, $date);
            self::assertSame('canceled', $date->getCanceled());
        }
    }

    /**
     * @test
     */
    public function returnsNotCanceledDatesOnMixedIntervals(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([
            [
                'start' => '2022-06-21T16:00:00+02:00',
                'end' => '2022-06-21T22:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'interval' => 1,
            ],
            [
                'start' => '2022-04-01T16:00:00+02:00',
                'end' => '2022-04-01T17:00:00+02:00',
                'repeatUntil' => '2022-04-03T18:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Daily',
                'interval' => 1,
            ],
            [
                'weekdays' => [
                    'Saturday',
                    'Sunday',
                ],
                'start' => '2022-03-02T11:00:00+01:00',
                'end' => '2022-03-02T13:00:00+01:00',
                'repeatUntil' => '2022-03-15T13:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Weekly',
                'interval' => 1,
            ],
        ], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(8, $result);

        foreach ($result as $date) {
            self::assertInstanceOf(Date::class, $date);
            self::assertSame('no', $date->getCanceled());
        }
    }

    private function createContext(\DateTimeImmutable $dateTime): Context
    {
        $context = new Context();
        $context->setAspect('date', new DateTimeAspect($dateTime));
        return $context;
    }
}

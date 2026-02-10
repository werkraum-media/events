<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Service\DestinationDataImportService;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Service\DestinationDataImportService\DatesFactory;

class DatesFactoryTest extends TestCase
{
    private function createTestSubject(
        string $contextDate
    ): DatesFactory {
        $logger = self::createStub(Logger::class);
        $logManager = self::createStub(LogManager::class);
        $logManager->method('getLogger')->willReturn($logger);

        return new DatesFactory(
            $this->createContext(new DateTimeImmutable($contextDate)),
            self::createStub(ConfigurationManager::class),
            $logManager
        );
    }

    #[Test]
    public function canBeCreated(): void
    {
        $subject = $this->createTestSubject('now');

        self::assertInstanceOf(
            DatesFactory::class,
            $subject
        );
    }

    #[DataProvider('possibleUnkownInput')]
    #[Test]
    public function returnsNoResultOnUnkownInput(array $unkownInput): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates($unkownInput, false);

        self::assertInstanceOf(Generator::class, $result);
        self::assertCount(0, iterator_to_array($result));
    }

    public static function possibleUnkownInput(): array
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

    #[Test]
    public function returnsSingleNotCanceledDate(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-04-01T16:00:00+02:00',
            'end' => '2022-04-01T17:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(Generator::class, $result);

        $firstEntry = $result->current();

        self::assertCount(1, iterator_to_array($result));

        self::assertInstanceOf(Date::class, $firstEntry);
        self::assertSame('2022-04-01T16:00:00+02:00', $firstEntry->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-04-01T17:00:00+02:00', $firstEntry->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $firstEntry->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(16, $result);
    }

    #[Test]
    public function returnsSingleCanceledDate(): void
    {
        $subject = $this->createTestSubject('2022-01-01T13:17:24 Europe/Berlin');

        $result = $subject->createDates([[
            'start' => '2022-04-01T16:00:00+02:00',
            'end' => '2022-04-01T17:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(Generator::class, $result);

        $firstEntry = $result->current();

        self::assertCount(1, iterator_to_array($result));

        self::assertInstanceOf(Date::class, $firstEntry);
        self::assertSame('2022-04-01T16:00:00+02:00', $firstEntry->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-04-01T17:00:00+02:00', $firstEntry->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $firstEntry->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(5, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('2022-10-29T16:00:00+02:00', $result[0]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-29T17:00:00+02:00', $result[0]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame('2022-11-02T16:00:00+01:00', $result[4]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-02T17:00:00+01:00', $result[4]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[4]->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(5, $result);

        self::assertInstanceOf(Date::class, $result[0]);

        self::assertSame('2022-10-29T16:00:00+02:00', $result[0]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-29T17:00:00+02:00', $result[0]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame('2022-11-02T16:00:00+01:00', $result[4]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-02T17:00:00+01:00', $result[4]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[4]->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('2022-10-29T16:00:00+02:00', $result[0]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-29T17:00:00+02:00', $result[0]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame('2022-11-05T16:00:00+01:00', $result[1]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-05T17:00:00+01:00', $result[1]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[1]->getCanceled());

        self::assertSame('2022-10-30T16:00:00+01:00', $result[2]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-30T17:00:00+01:00', $result[2]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[2]->getCanceled());

        self::assertSame('2022-11-06T16:00:00+01:00', $result[3]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-06T17:00:00+01:00', $result[3]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('canceled', $result[3]->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('2022-10-29T16:00:00+02:00', $result[0]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-29T17:00:00+02:00', $result[0]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame('2022-11-05T16:00:00+01:00', $result[1]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-05T17:00:00+01:00', $result[1]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[1]->getCanceled());

        self::assertSame('2022-10-30T16:00:00+01:00', $result[2]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-10-30T17:00:00+01:00', $result[2]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[2]->getCanceled());

        self::assertSame('2022-11-06T16:00:00+01:00', $result[3]->getStart()->format(DateTimeImmutable::ATOM));
        self::assertSame('2022-11-06T17:00:00+01:00', $result[3]->getEnd()->format(DateTimeImmutable::ATOM));
        self::assertSame('no', $result[3]->getCanceled());
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(8, $result);

        foreach ($result as $date) {
            self::assertInstanceOf(Date::class, $date);
            self::assertSame('canceled', $date->getCanceled());
        }
    }

    #[Test]
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

        self::assertInstanceOf(Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(8, $result);

        foreach ($result as $date) {
            self::assertInstanceOf(Date::class, $date);
            self::assertSame('no', $date->getCanceled());
        }
    }

    private function createContext(DateTimeImmutable $dateTime): Context
    {
        $context = new Context();
        $context->setAspect('date', new DateTimeAspect($dateTime));
        return $context;
    }
}

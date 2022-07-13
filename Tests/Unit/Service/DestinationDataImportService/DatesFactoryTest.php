<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Context\Context;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Service\DestinationDataImportService\DatesFactory;
use Wrm\Events\Tests\ProphecyTrait;

/**
 * @covers \Wrm\Events\Service\DestinationDataImportService\DatesFactory
 */
class DatesFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        self::assertInstanceOf(
            DatesFactory::class,
            $subject
        );
    }

    /**
     * @test
     * @dataProvider possibleUnkownInput
     */
    public function returnsNoResultOnUnkownInput(array $unkownInput): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates($unkownInput, false);

        self::assertInstanceOf(\Generator::class, $result);
        self::assertCount(0, $result);
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
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'start' => '2099-06-21T16:00:00+02:00',
            'end' => '2099-06-21T22:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);

        $firstEntry = $result->current();

        self::assertCount(1, $result);

        self::assertInstanceOf(Date::class, $firstEntry);
        self::assertSame('4085733600', $firstEntry->getStart()->format('U'));
        self::assertSame('4085755200', $firstEntry->getEnd()->format('U'));
        self::assertSame('no', $firstEntry->getCanceled());
    }

    /**
     * @test
     */
    public function returnsSingleCanceledDate(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'start' => '2099-06-21T16:00:00+02:00',
            'end' => '2099-06-21T22:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(1, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('4085733600', $result[0]->getStart()->format('U'));
        self::assertSame('4085755200', $result[0]->getEnd()->format('U'));
        self::assertSame('canceled', $result[0]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnDailyBasis(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'start' => '2099-04-01T16:00:00+02:00',
            'end' => '2099-04-01T17:00:00+02:00',
            'repeatUntil' => '2099-04-03T18:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Daily',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(3, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('4078735200', $result[0]->getStart()->format('U'));
        self::assertSame('4078738800', $result[0]->getEnd()->format('U'));
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame('4078908000', $result[2]->getStart()->format('U'));
        self::assertSame('4078911600', $result[2]->getEnd()->format('U'));
        self::assertSame('canceled', $result[2]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsNotCanceledDatesOnDailyBasis(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'start' => '2099-04-01T16:00:00+02:00',
            'end' => '2099-04-01T17:00:00+02:00',
            'repeatUntil' => '2099-04-03T18:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Daily',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(3, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('4078735200', $result[0]->getStart()->format('U'));
        self::assertSame('4078738800', $result[0]->getEnd()->format('U'));
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame('4078908000', $result[2]->getStart()->format('U'));
        self::assertSame('4078911600', $result[2]->getEnd()->format('U'));
        self::assertSame('no', $result[2]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnWeeklyBasis(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'weekdays' => [
                'Saturday',
                'Sunday',
            ],
            'start' => '2099-03-02T11:00:00+01:00',
            'end' => '2099-03-02T13:00:00+01:00',
            'repeatUntil' => '2099-03-15T13:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Weekly',
            'interval' => 1,
        ]], true);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('4076560800', $result[0]->getStart()->format('U'));
        self::assertSame('4076568000', $result[0]->getEnd()->format('U'));
        self::assertSame('canceled', $result[0]->getCanceled());

        self::assertSame('4077252000', $result[3]->getStart()->format('U'));
        self::assertSame('4077259200', $result[3]->getEnd()->format('U'));
        self::assertSame('canceled', $result[3]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsNotCanceledDatesOnWeeklyBasis(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([[
            'weekdays' => [
                'Saturday',
                'Sunday',
            ],
            'start' => '2099-03-02T11:00:00+01:00',
            'end' => '2099-03-02T13:00:00+01:00',
            'repeatUntil' => '2099-03-15T13:00:00+02:00',
            'tz' => 'Europe/Berlin',
            'freq' => 'Weekly',
            'interval' => 1,
        ]], false);

        self::assertInstanceOf(\Generator::class, $result);
        $result = iterator_to_array($result);

        self::assertCount(4, $result);

        self::assertInstanceOf(Date::class, $result[0]);
        self::assertSame('4076560800', $result[0]->getStart()->format('U'));
        self::assertSame('4076568000', $result[0]->getEnd()->format('U'));
        self::assertSame('no', $result[0]->getCanceled());

        self::assertSame('4077252000', $result[3]->getStart()->format('U'));
        self::assertSame('4077259200', $result[3]->getEnd()->format('U'));
        self::assertSame('no', $result[3]->getCanceled());
    }

    /**
     * @test
     */
    public function returnsCanceledDatesOnMixedIntervals(): void
    {
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([
            [
                'start' => '2099-06-21T16:00:00+02:00',
                'end' => '2099-06-21T22:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'interval' => 1,
            ],
            [
                'start' => '2099-04-01T16:00:00+02:00',
                'end' => '2099-04-01T17:00:00+02:00',
                'repeatUntil' => '2099-04-03T18:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Daily',
                'interval' => 1,
            ],
            [
                'weekdays' => [
                    'Saturday',
                    'Sunday',
                ],
                'start' => '2099-03-02T11:00:00+01:00',
                'end' => '2099-03-02T13:00:00+01:00',
                'repeatUntil' => '2099-03-15T13:00:00+02:00',
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
        $context = $this->prophesize(Context::class);

        $subject = new DatesFactory(
            $context->reveal()
        );

        $result = $subject->createDates([
            [
                'start' => '2099-06-21T16:00:00+02:00',
                'end' => '2099-06-21T22:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'interval' => 1,
            ],
            [
                'start' => '2099-04-01T16:00:00+02:00',
                'end' => '2099-04-01T17:00:00+02:00',
                'repeatUntil' => '2099-04-03T18:00:00+02:00',
                'tz' => 'Europe/Berlin',
                'freq' => 'Daily',
                'interval' => 1,
            ],
            [
                'weekdays' => [
                    'Saturday',
                    'Sunday',
                ],
                'start' => '2099-03-02T11:00:00+01:00',
                'end' => '2099-03-02T13:00:00+01:00',
                'repeatUntil' => '2099-03-15T13:00:00+02:00',
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
}

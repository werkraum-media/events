<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model;

use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Date;

class DateTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new Date();

        self::assertInstanceOf(
            Date::class,
            $subject
        );
    }

    #[Test]
    public function returnsThatItHasUsefulStartTime(): void
    {
        $subject = new Date();
        $subject->setStart(new DateTime('2022-07-11T13:48:00'));

        self::assertTrue($subject->getHasUsefulStartTime());
    }

    #[Test]
    public function returnsThatItDoesNotHaveUsefulStartTime(): void
    {
        $subject = new Date();
        $subject->setStart(new DateTime('2022-07-11T00:00:00'));

        self::assertFalse($subject->getHasUsefulStartTime());
    }

    #[Test]
    public function returnsThatItHasUsefulEndTime(): void
    {
        $subject = new Date();
        $subject->setEnd(new DateTime('2022-07-11T00:00:00'));

        self::assertTrue($subject->getHasUsefulEndTime());
    }

    #[Test]
    public function returnsThatItDoesNotHaveUsefulEndTimeWithTime(): void
    {
        $subject = new Date();
        $subject->setEnd(new DateTime('2022-07-11T23:59:00'));

        self::assertFalse($subject->getHasUsefulEndTime());
    }

    #[Test]
    public function returnsThatItDoesNotHaveUsefulEndTimeWithNull(): void
    {
        $subject = new Date();
        $subject->setEnd(null);

        self::assertFalse($subject->getHasUsefulEndTime());
    }

    #[Test]
    public function returnsThatItEndsOnSameDay(): void
    {
        $subject = new Date();
        $subject->setStart(new DateTime('2022-07-11T14:00:00'));
        $subject->setEnd(new DateTime('2022-07-11T22:00:00'));

        self::assertTrue($subject->getEndsOnSameDay());
    }

    #[Test]
    public function returnsThatItDoesNotEndOnSameDayWithDifferentDates(): void
    {
        $subject = new Date();
        $subject->setStart(new DateTime('2022-07-11T14:00:00'));
        $subject->setEnd(new DateTime('2022-07-13T22:00:00'));

        self::assertFalse($subject->getEndsOnSameDay());
    }

    #[Test]
    public function returnsThatItDoesNotEndOnSameDayWithMissingEnd(): void
    {
        $subject = new Date();
        $subject->setStart(new DateTime('2022-07-11T14:00:00'));
        $subject->setEnd(null);

        self::assertFalse($subject->getEndsOnSameDay());
    }

    #[Test]
    public function returnsNullAsEnd(): void
    {
        $subject = new Date();
        $subject->setEnd(null);

        self::assertNull($subject->getEnd());
    }

    #[Test]
    public function returnsEnd(): void
    {
        $end = new DateTime('2022-07-13T22:00:00');
        $subject = new Date();
        $subject->setEnd($end);

        self::assertSame($end, $subject->getEnd());
    }
}

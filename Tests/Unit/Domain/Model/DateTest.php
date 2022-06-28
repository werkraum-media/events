<?php

namespace Wrm\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Wrm\Events\Domain\Model\Date;

/**
 * @covers \Wrm\Events\Domain\Model\Date
 */
class DateTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Date();

        self::assertInstanceOf(
            Date::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsThatItHasUsefulStartTime(): void
    {
        $subject = new Date();
        $subject->setStart(new \DateTime('2022-07-11T13:48:00'));

        self::assertTrue($subject->getHasUsefulStartTime());
    }

    /**
     * @test
     */
    public function returnsThatItDoesNotHaveUsefulStartTime(): void
    {
        $subject = new Date();
        $subject->setStart(new \DateTime('2022-07-11T00:00:00'));

        self::assertFalse($subject->getHasUsefulStartTime());
    }

    /**
     * @test
     */
    public function returnsThatItHasUsefulEndTime(): void
    {
        $subject = new Date();
        $subject->setEnd(new \DateTime('2022-07-11T00:00:00'));

        self::assertTrue($subject->getHasUsefulEndTime());
    }

    /**
     * @test
     */
    public function returnsThatItDoesNotHaveUsefulEndTime(): void
    {
        $subject = new Date();
        $subject->setEnd(new \DateTime('2022-07-11T23:59:00'));

        self::assertFalse($subject->getHasUsefulEndTime());
    }

    /**
     * @test
     */
    public function returnsThatItEndsOnSameDay(): void
    {
        $subject = new Date();
        $subject->setStart(new \DateTime('2022-07-11T14:00:00'));
        $subject->setEnd(new \DateTime('2022-07-11T22:00:00'));

        self::assertTrue($subject->getEndsOnSameDay());
    }

    /**
     * @test
     */
    public function returnsThatItDoesNotEndOnSameDay(): void
    {
        $subject = new Date();
        $subject->setStart(new \DateTime('2022-07-11T14:00:00'));
        $subject->setEnd(new \DateTime('2022-07-13T22:00:00'));

        self::assertFalse($subject->getEndsOnSameDay());
    }
}

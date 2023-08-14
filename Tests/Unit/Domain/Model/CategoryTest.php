<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use Wrm\Events\Domain\Model\Category;

/**
 * @covers \Wrm\Events\Domain\Model\Category
 */
class CategoryTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Category(
            null,
            10,
            'Title',
            false
        );

        self::assertInstanceOf(
            Category::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsSorting(): void
    {
        $subject = new Category(
            null,
            10,
            'Title',
            false
        );
        $subject->_setProperty('sorting', 10);

        self::assertSame(10, $subject->getSorting());
    }

    /**
     * @test
     */
    public function canBeVisible(): void
    {
        $subject = new Category(
            null,
            10,
            'Title',
            false
        );

        self::assertFalse($subject->_getProperty('hidden'));
    }

    /**
     * @test
     */
    public function canHide(): void
    {
        $subject = new Category(
            null,
            10,
            'Title',
            true
        );

        self::assertTrue($subject->_getProperty('hidden'));
    }
}

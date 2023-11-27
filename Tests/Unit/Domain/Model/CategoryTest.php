<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Category;

class CategoryTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Wrm\Events\Domain\Model\Category;
use Wrm\Events\Domain\Model\Event;

/**
 * @covers \Wrm\Events\Domain\Model\Event
 */
class EventTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Event();

        self::assertInstanceOf(
            Event::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsSortedFeatures(): void
    {
        $feature1 = $this->createStub(Category::class);
        $feature1->method('getSorting')->willReturn(10);
        $feature2 = $this->createStub(Category::class);
        $feature2->method('getSorting')->willReturn(5);

        $storage = new ObjectStorage();
        $storage->attach($feature1);
        $storage->attach($feature2);

        $subject = new Event();
        $subject->_setProperty('features', $storage);

        self::assertSame([
            $feature2,
            $feature1,
        ], $subject->getFeatures());
    }

    /**
     * @test
     */
    public function returnsEmptyFeaturesStorage(): void
    {
        $subject = new Event();
        $subject->_setProperty('features', new ObjectStorage());

        self::assertSame([], $subject->getFeatures());
    }
}

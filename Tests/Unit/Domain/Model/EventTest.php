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
        $feature1 = new Category();
        $feature1->_setProperty('sorting', 10);
        $feature2 = new Category();
        $feature2->_setProperty('sorting', 5);

        $storage = new ObjectStorage();
        $storage->attach($feature1);
        $storage->attach($feature2);

        $subject = new Event();
        $subject->setFeatures($storage);

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
        $subject->setFeatures(new ObjectStorage());

        self::assertSame([], $subject->getFeatures());
    }
}

<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

class EventTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function returnsSortedFeatures(): void
    {
        $feature1 = self::createStub(Category::class);
        $feature1->method('getSorting')->willReturn(10);
        $feature2 = self::createStub(Category::class);
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

    #[Test]
    public function returnsEmptyFeaturesStorage(): void
    {
        $subject = new Event();
        $subject->_setProperty('features', new ObjectStorage());

        self::assertSame([], $subject->getFeatures());
    }
}

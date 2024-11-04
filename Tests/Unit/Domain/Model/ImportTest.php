<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Resource\Folder;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Model\Import;
use WerkraumMedia\Events\Domain\Model\Region;

class ImportTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
        );

        self::assertInstanceOf(
            Import::class,
            $subject
        );
    }

    #[Test]
    public function returnsRestExperience(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            'experience',
        );

        self::assertSame(
            'experience',
            $subject->getRestExperience()
        );
    }

    #[Test]
    public function returnsStoragePid(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            20,
            '',
            '',
        );

        self::assertSame(
            20,
            $subject->getStoragePid()
        );
    }

    #[Test]
    public function returnsRegion(): void
    {
        $folder = $this->createStub(Folder::class);
        $region = $this->createStub(Region::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            '',
            0,
            null,
            0,
            null,
            $region
        );

        self::assertSame(
            $region,
            $subject->getRegion()
        );
    }

    #[Test]
    public function returnsFilesFolder(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
        );

        self::assertSame(
            $folder,
            $subject->getFilesFolder()
        );
    }

    #[Test]
    public function returnsCategoriesPid(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            '',
            10
        );

        self::assertSame(
            10,
            $subject->getCategoriesPid()
        );
    }

    #[Test]
    public function returnsCategoryParent(): void
    {
        $category = $this->createStub(Category::class);
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            '',
            0,
            $category
        );

        self::assertSame(
            $category,
            $subject->getCategoryParent()
        );
    }

    #[Test]
    public function returnsFeaturesPid(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            '',
            0,
            null,
            10
        );

        self::assertSame(
            10,
            $subject->getFeaturesPid()
        );
    }

    #[Test]
    public function returnsFeaturesParent(): void
    {
        $feature = $this->createStub(Category::class);
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            '',
            0,
            null,
            0,
            $feature
        );

        self::assertSame(
            $feature,
            $subject->getFeaturesParent()
        );
    }

    #[Test]
    public function returnsSearchQuery(): void
    {
        $folder = $this->createStub(Folder::class);

        $subject = new Import(
            $folder,
            0,
            '',
            '',
            'next_months,12',
            500,
            'name:"Test"'
        );

        self::assertSame(
            'name:"Test"',
            $subject->getRestSearchQuery()
        );
    }
}

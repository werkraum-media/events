<?php

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Resource\Folder;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Model\Import;
use WerkraumMedia\Events\Domain\Model\Region;
use WerkraumMedia\Events\Tests\ProphecyTrait;

/**
 * @covers \WerkraumMedia\Events\Domain\Model\Import
 */
class ImportTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            ''
        );

        self::assertInstanceOf(
            Import::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsRestExperience(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            'experience'
        );

        self::assertSame(
            'experience',
            $subject->getRestExperience()
        );
    }

    /**
     * @test
     */
    public function returnsStoragePid(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            20,
            ''
        );

        self::assertSame(
            20,
            $subject->getStoragePid()
        );
    }

    /**
     * @test
     */
    public function returnsRegion(): void
    {
        $folder = $this->prophesize(Folder::class);
        $region = $this->prophesize(Region::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
            '',
            0,
            null,
            0,
            null,
            $region->reveal()
        );

        self::assertSame(
            $region->reveal(),
            $subject->getRegion()
        );
    }

    /**
     * @test
     */
    public function returnsFilesFolder(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            ''
        );

        self::assertSame(
            $folder->reveal(),
            $subject->getFilesFolder()
        );
    }

    /**
     * @test
     */
    public function returnsCategoriesPid(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
            '',
            10
        );

        self::assertSame(
            10,
            $subject->getCategoriesPid()
        );
    }

    /**
     * @test
     */
    public function returnsCategoryParent(): void
    {
        $category = $this->prophesize(Category::class);
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
            '',
            0,
            $category->reveal()
        );

        self::assertSame(
            $category->reveal(),
            $subject->getCategoryParent()
        );
    }

    /**
     * @test
     */
    public function returnsFeaturesPid(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
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

    /**
     * @test
     */
    public function returnsFeaturesParent(): void
    {
        $feature = $this->prophesize(Category::class);
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
            '',
            0,
            null,
            0,
            $feature->reveal()
        );

        self::assertSame(
            $feature->reveal(),
            $subject->getFeaturesParent()
        );
    }

    /**
     * @test
     */
    public function returnsSearchQuery(): void
    {
        $folder = $this->prophesize(Folder::class);

        $subject = new Import(
            $folder->reveal(),
            0,
            '',
            'name:"Test"'
        );

        self::assertSame(
            'name:"Test"',
            $subject->getSearchQuery()
        );
    }
}

<?php

namespace Wrm\Events\Tests\Unit\Domain\Model;

use TYPO3\CMS\Core\Resource\Folder;
use Wrm\Events\Domain\Model\Category;
use Wrm\Events\Domain\Model\Import;
use PHPUnit\Framework\TestCase;
use Wrm\Events\Domain\Model\Region;
use Wrm\Events\Tests\ProphecyTrait;


/**
 * @covers \Wrm\Events\Domain\Model\Import
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

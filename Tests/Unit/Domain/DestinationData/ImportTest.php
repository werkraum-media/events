<?php

namespace Wrm\Events\Tests\Unit\Domain\DestinationData;

use Wrm\Events\Domain\DestinationData\Import;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Wrm\Events\Domain\DestinationData\Import
 */
class ImportTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Import(
            '',
            0,
            null,
            '',
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
        $subject = new Import(
            'experience',
            0,
            null,
            '',
            ''
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
        $subject = new Import(
            '',
            20,
            null,
            '',
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
    public function returnsRegionUid(): void
    {
        $subject = new Import(
            '',
            0,
            30,
            '',
            ''
        );

        self::assertSame(
            30,
            $subject->getRegionUid()
        );
    }

    /**
     * @test
     */
    public function returnsFilesFolder(): void
    {
        $subject = new Import(
            '',
            0,
            null,
            'test/folder',
            ''
        );

        self::assertSame(
            'test/folder',
            $subject->getFilesFolder()
        );
    }

    /**
     * @test
     */
    public function returnsSearchQuery(): void
    {
        $subject = new Import(
            '',
            0,
            null,
            'test/folder',
            'name:"Test"'
        );

        self::assertSame(
            'name:"Test"',
            $subject->getSearchQuery()
        );
    }
}

<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Events\Controller;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Events\Controller\DateSearchVariables;
use WerkraumMedia\Events\Tests\ProphecyTrait;

/**
 * @covers \WerkraumMedia\Events\Events\Controller\DateSearchVariables
 */
class DateSearchVariablesTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            [],
            []
        );

        self::assertInstanceOf(
            DateSearchVariables::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsInitializeSearch(): void
    {
        $subject = new DateSearchVariables(
            [
                'executed' => '1',
            ],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            [],
            []
        );

        self::assertSame(
            [
                'executed' => '1',
            ],
            $subject->getSearch()
        );
    }

    /**
     * @test
     */
    public function returnsInitializeDemand(): void
    {
        $demand = new DateDemand();
        $subject = new DateSearchVariables(
            [
            ],
            $demand,
            $this->prophesize(QueryResult::class)->reveal(),
            [],
            []
        );

        self::assertSame(
            $demand,
            $subject->getDemand()
        );
    }

    /**
     * @test
     */
    public function returnsInitialRegions(): void
    {
        $regions = $this->prophesize(QueryResult::class)->reveal();
        $subject = new DateSearchVariables(
            [
            ],
            new DateDemand(),
            $regions,
            [],
            []
        );

        self::assertSame(
            $regions,
            $subject->getRegions()
        );
    }

    /**
     * @test
     */
    public function returnsInitialCategories(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            [
                ['example category'],
            ],
            []
        );

        self::assertSame(
            [
                ['example category'],
            ],
            $subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function returnsInitialFeatures(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            [
            ],
            [
                ['example feature category'],
            ]
        );

        self::assertSame(
            [
                ['example feature category'],
            ],
            $subject->getFeatures()
        );
    }

    /**
     * @test
     */
    public function returnsInitialVariablesForView(): void
    {
        $demand = new DateDemand();
        $regions = $this->prophesize(QueryResult::class)->reveal();
        $subject = new DateSearchVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $regions,
            [
                ['example category category'],
            ],
            [
                ['example feature category'],
            ]
        );

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'regions' => $regions,
                'categories' => [
                    ['example category category'],
                ],
                'features' => [
                    ['example feature category'],
                ],
            ],
            $subject->getVariablesForView()
        );
    }

    /**
     * @test
     */
    public function returnsVariablesForViewWithAddedVariables(): void
    {
        $demand = new DateDemand();
        $regions = $this->prophesize(QueryResult::class)->reveal();
        $subject = new DateSearchVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $regions,
            [
                ['example category category'],
            ],
            [
                ['example feature category'],
            ]
        );

        $subject->addVariable('variable 1', 'Value 1');
        $subject->addVariable('variable 2', 'Value 2');

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'regions' => $regions,
                'categories' => [
                    ['example category category'],
                ],
                'features' => [
                    ['example feature category'],
                ],
                'variable 1' => 'Value 1',
                'variable 2' => 'Value 2',
            ],
            $subject->getVariablesForView()
        );
    }

    /**
     * @test
     */
    public function returnsVariablesForViewWithOverwrittenVariables(): void
    {
        $demand = new DateDemand();
        $regions = $this->prophesize(QueryResult::class)->reveal();
        $subject = new DateSearchVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $regions,
            [
                ['example category category'],
            ],
            [
                ['example feature category'],
            ]
        );

        $subject->addVariable('variable 1', 'Value 1');
        $subject->addVariable('variable 1', 'Value 2');
        $subject->addVariable('features', 'Value 2');

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'regions' => $regions,
                'categories' => [
                    ['example category category'],
                ],
                'features' => 'Value 2',
                'variable 1' => 'Value 2',
            ],
            $subject->getVariablesForView()
        );
    }
}

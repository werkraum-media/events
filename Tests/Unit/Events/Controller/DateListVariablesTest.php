<?php

declare(strict_types=1);

namespace WerkraumMedia\Tests\Unit\Events\Controller;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Events\Controller\DateListVariables;
use WerkraumMedia\Events\Tests\ProphecyTrait;

/**
 * @covers \WerkraumMedia\Events\Events\Controller\DateListVariables
 */
class DateListVariablesTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new DateListVariables(
            [],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            $this->prophesize(PaginationInterface::class)->reveal()
        );

        self::assertInstanceOf(
            DateListVariables::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function returnsInitialSearch(): void
    {
        $subject = new DateListVariables(
            [
                'executed' => '1',
            ],
            new DateDemand(),
            $this->prophesize(QueryResult::class)->reveal(),
            $this->prophesize(PaginationInterface::class)->reveal()
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
    public function returnsInitialDemand(): void
    {
        $demand = new DateDemand();
        $subject = new DateListVariables(
            [
            ],
            $demand,
            $this->prophesize(QueryResult::class)->reveal(),
            $this->prophesize(PaginationInterface::class)->reveal()
        );

        self::assertSame(
            $demand,
            $subject->getDemand()
        );
    }

    /**
     * @test
     */
    public function returnsInitialDates(): void
    {
        $dates = $this->prophesize(QueryResult::class)->reveal();
        $subject = new DateListVariables(
            [
            ],
            new DateDemand(),
            $dates,
            $this->prophesize(PaginationInterface::class)->reveal()
        );

        self::assertSame(
            $dates,
            $subject->getDates()
        );
    }

    /**
     * @test
     */
    public function returnsInitialVariablesForView(): void
    {
        $demand = new DateDemand();
        $dates = $this->prophesize(QueryResult::class)->reveal();
        $pagination = $this->prophesize(PaginationInterface::class)->reveal();
        $subject = new DateListVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $dates,
            $pagination
        );

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'dates' => $dates,
                'pagination' => $pagination,
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
        $dates = $this->prophesize(QueryResult::class)->reveal();
        $pagination = $this->prophesize(PaginationInterface::class)->reveal();
        $subject = new DateListVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $dates,
            $pagination
        );

        $subject->addVariable('variable 1', 'Value 1');
        $subject->addVariable('variable 2', 'Value 2');

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'dates' => $dates,
                'pagination' => $pagination,
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
        $dates = $this->prophesize(QueryResult::class)->reveal();
        $pagination = $this->prophesize(PaginationInterface::class)->reveal();
        $subject = new DateListVariables(
            [
                'executed' => '1',
            ],
            $demand,
            $dates,
            $pagination
        );

        $subject->addVariable('variable 1', 'Value 1');
        $subject->addVariable('variable 1', 'Value 2');
        $subject->addVariable('dates', 'Value 2');

        self::assertSame(
            [
                'search' => [
                    'executed' => '1',
                ],
                'demand' => $demand,
                'dates' => 'Value 2',
                'pagination' => $pagination,
                'variable 1' => 'Value 2',
            ],
            $subject->getVariablesForView()
        );
    }
}

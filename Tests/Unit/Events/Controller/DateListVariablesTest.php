<?php

declare(strict_types=1);

namespace WerkraumMedia\Tests\Unit\Events\Controller;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Events\Controller\DateListVariables;

class DateListVariablesTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new DateListVariables(
            [],
            new DateDemand(),
            $this->createStub(QueryResult::class),
            $this->createStub(PaginationInterface::class)
        );

        self::assertInstanceOf(
            DateListVariables::class,
            $subject
        );
    }

    #[Test]
    public function returnsInitialSearch(): void
    {
        $subject = new DateListVariables(
            [
                'executed' => '1',
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
            $this->createStub(PaginationInterface::class)
        );

        self::assertSame(
            [
                'executed' => '1',
            ],
            $subject->getSearch()
        );
    }

    #[Test]
    public function returnsInitialDemand(): void
    {
        $demand = new DateDemand();
        $subject = new DateListVariables(
            [
            ],
            $demand,
            $this->createStub(QueryResult::class),
            $this->createStub(PaginationInterface::class)
        );

        self::assertSame(
            $demand,
            $subject->getDemand()
        );
    }

    #[Test]
    public function returnsInitialDates(): void
    {
        $dates = $this->createStub(QueryResult::class);
        $subject = new DateListVariables(
            [
            ],
            new DateDemand(),
            $dates,
            $this->createStub(PaginationInterface::class)
        );

        self::assertSame(
            $dates,
            $subject->getDates()
        );
    }

    #[Test]
    public function returnsInitialVariablesForView(): void
    {
        $demand = new DateDemand();
        $dates = $this->createStub(QueryResult::class);
        $pagination = $this->createStub(PaginationInterface::class);
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

    #[Test]
    public function returnsVariablesForViewWithAddedVariables(): void
    {
        $demand = new DateDemand();
        $dates = $this->createStub(QueryResult::class);
        $pagination = $this->createStub(PaginationInterface::class);
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

    #[Test]
    public function returnsVariablesForViewWithOverwrittenVariables(): void
    {
        $demand = new DateDemand();
        $dates = $this->createStub(QueryResult::class);
        $pagination = $this->createStub(PaginationInterface::class);
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

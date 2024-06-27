<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Events\Controller;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Events\Controller\DateSearchVariables;

class DateSearchVariablesTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            [
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
            [],
            []
        );

        self::assertInstanceOf(
            DateSearchVariables::class,
            $subject
        );
    }

    #[Test]
    public function returnsInitializeSettings(): void
    {
        $subject = new DateSearchVariables(
            [
                'someCustomKey' => 'someCustomValue',
            ],
            [
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
            [],
            []
        );

        self::assertSame(
            [
                'someCustomKey' => 'someCustomValue',
            ],
            $subject->getSettings()
        );
    }

    #[Test]
    public function returnsInitializeSearch(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            [
                'executed' => '1',
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
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

    #[Test]
    public function returnsInitializeDemand(): void
    {
        $demand = new DateDemand();
        $subject = new DateSearchVariables(
            [
            ],
            [
            ],
            $demand,
            $this->createStub(QueryResult::class),
            [],
            []
        );

        self::assertSame(
            $demand,
            $subject->getDemand()
        );
    }

    #[Test]
    public function returnsInitialRegions(): void
    {
        $regions = $this->createStub(QueryResult::class);
        $subject = new DateSearchVariables(
            [
            ],
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

    #[Test]
    public function returnsInitialCategories(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            [
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
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

    #[Test]
    public function returnsInitialFeatures(): void
    {
        $subject = new DateSearchVariables(
            [
            ],
            [
            ],
            new DateDemand(),
            $this->createStub(QueryResult::class),
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

    #[Test]
    public function returnsInitialVariablesForView(): void
    {
        $demand = new DateDemand();
        $regions = $this->createStub(QueryResult::class);
        $subject = new DateSearchVariables(
            [
            ],
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

    #[Test]
    public function returnsVariablesForViewWithAddedVariables(): void
    {
        $demand = new DateDemand();
        $regions = $this->createStub(QueryResult::class);
        $subject = new DateSearchVariables(
            [
            ],
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

    #[Test]
    public function returnsVariablesForViewWithOverwrittenVariables(): void
    {
        $demand = new DateDemand();
        $regions = $this->createStub(QueryResult::class);
        $subject = new DateSearchVariables(
            [
            ],
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

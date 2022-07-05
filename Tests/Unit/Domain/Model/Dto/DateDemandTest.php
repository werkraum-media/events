<?php

namespace Wrm\Events\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\TestCase;
use Wrm\Events\Domain\Model\Dto\DateDemand;

/**
 * @covers \Wrm\Events\Domain\Model\Dto\DateDemand
 */
class DateDemandTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new DateDemand();

        self::assertInstanceOf(
            DateDemand::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function searchWordIsSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'searchword' => 'This is the search word',
            ],
            [
            ]
        );

        self::assertSame(
            'This is the search word',
            $result->getSearchword()
        );
    }

    /**
     * @test
     */
    public function synonymsAreSetBySettings(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'searchword' => 'synonym1',
            ],
            [
                'synonyms' => [
                    [
                        'word' => 'Word1',
                        'synonyms' => 'synonym1, synonym2',
                    ],
                    [
                        'word' => 'Word2',
                        'synonyms' => 'synonym3, synonym4',
                    ],
                    [
                        'word' => 'Word3',
                        'synonyms' => 'synonym1',
                    ],
                ],
            ]
        );

        self::assertSame(
            [
                'Word1',
                'Word3',
            ],
            $result->getSynonymsForSearchword()
        );
    }

    /**
     * @test
     */
    public function categoriesAreSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'userCategories' => [
                    '10', '20',
                ],
            ],
            [
            ]
        );

        self::assertSame(
            [
                10,
                20,
            ],
            $result->getUserCategories()
        );
    }

    /**
     * @test
     */
    public function featuresAreSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'features' => [
                    '10', '20',
                ],
            ],
            [
            ]
        );

        self::assertSame(
            [
                10,
                20,
            ],
            $result->getFeatures()
        );
    }

    /**
     * @test
     */
    public function regionsAreSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'regions' => [
                    '10', '20',
                ],
            ],
            [
            ]
        );

        self::assertSame(
            [
                10,
                20,
            ],
            $result->getRegions()
        );
    }
}

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
    public function regionIsSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'region' => '10',
            ],
            [
            ]
        );

        self::assertSame(
            [
                10,
            ],
            $result->getRegions()
        );
        self::assertSame(
            '10',
            $result->getRegion()
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
        self::assertSame(
            '10,20',
            $result->getRegion()
        );
    }

    /**
     * @test
     */
    public function startIsSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'start' => '2022-07-12',
            ],
            [
            ]
        );

        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $result->getStartObject()
        );
        self::assertSame(
            '2022-07-12',
            $result->getStartObject()->format('Y-m-d')
        );
        self::assertSame(
            '2022-07-12',
            $result->getStart()
        );
    }

    /**
     * @test
     */
    public function endIsSetByRequest(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'end' => '2022-07-12',
            ],
            [
            ]
        );

        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $result->getEndObject()
        );
        self::assertSame(
            '2022-07-12',
            $result->getEndObject()->format('Y-m-d')
        );
        self::assertSame(
            '2022-07-12',
            $result->getEnd()
        );
    }

    /**
     * @test
     * @dataProvider possibleEndAndStartNullCombinations
     */
    public function returnsEndsOnSameDayIfAnyIsNull(
        string $start,
        string $end
    ): void {
        $result = DateDemand::createFromRequestValues(
            [
                'start' => $start,
                'end' => $end,
            ],
            [
            ]
        );

        self::assertTrue(
            $result->getEndsOnSameDay()
        );
    }

    public function possibleEndAndStartNullCombinations(): \Generator
    {
        yield 'Both are empty' => [
            'start' => '',
            'end' => '',
        ];
        yield 'Start is empty' => [
            'start' => '',
            'end' => '2022-07-12',
        ];
        yield 'End is empty' => [
            'start' => '2022-07-12',
            'end' => '',
        ];
    }

    /**
     * @test
     */
    public function returnsEndsOnSameDayIfBothAreOnSameDay(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'start' => '2022-07-12',
                'end' => '2022-07-12',
            ],
            [
            ]
        );

        self::assertTrue(
            $result->getEndsOnSameDay()
        );
    }

    /**
     * @test
     */
    public function returnsEndsOnSameDayIfBothAreOnDifferentDays(): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'start' => '2022-07-12',
                'end' => '2022-07-13',
            ],
            [
            ]
        );

        self::assertFalse(
            $result->getEndsOnSameDay()
        );
    }

    /**
     * @test
     * @dataProvider possibleSubmittedHighlights
     *
     * @param mixed $highlight
     */
    public function returnsHighlightIfSet($highlight): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'highlight' => $highlight,
            ],
            [
            ]
        );

        self::assertTrue($result->getHighlight());
    }

    public function possibleSubmittedHighlights(): \Generator
    {
        yield 'true' => ['highlight' => true];
        yield '1 as integer' => ['highlight' => 1];
        yield '1 as string' => ['highlight' => '1'];
    }

    /**
     * @test
     * @dataProvider possibleSubmittedFalsyHighlights
     *
     * @param mixed $highlight
     */
    public function returnsNoHighlightIfNotSet($highlight): void
    {
        $result = DateDemand::createFromRequestValues(
            [
                'highlight' => $highlight,
            ],
            [
            ]
        );

        self::assertFalse($result->getHighlight());
    }

    public function possibleSubmittedFalsyHighlights(): \Generator
    {
        yield 'false' => ['highlight' => false];
        yield '0 as integer' => ['highlight' => 0];
        yield '0 as string' => ['highlight' => '0'];
        yield 'empty string' => ['highlight' => ''];
    }
}

<?php

declare(strict_types=1);

/*
 * Copyright (C) 2022 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace Wrm\Events\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Dto\DateDemandFactory;
use Wrm\Events\Tests\ProphecyTrait;

/**
 * @covers \Wrm\Events\Domain\Model\Dto\DateDemandFactory
 */
class DateDemandFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );

        self::assertInstanceOf(
            DateDemandFactory::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function fromSettingsDoesNotThrowUndefinedArrayKeyWarnings(): void
    {
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );

        $result = $subject->fromSettings([]);

        self::assertInstanceOf(
            DateDemand::class,
            $result
        );
    }

    /**
     * @test
     */
    public function searchWordIsSetByRequest(): void
    {
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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
        $typoScriptService = $this->createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
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

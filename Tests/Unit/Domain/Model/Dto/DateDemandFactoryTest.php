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

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model\Dto;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemandFactory;

class DateDemandFactoryTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );

        self::assertInstanceOf(
            DateDemandFactory::class,
            $subject
        );
    }

    #[Test]
    public function fromSettingsDoesNotThrowUndefinedArrayKeyWarnings(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );

        $result = $subject->fromSettings([]);

        self::assertInstanceOf(
            DateDemand::class,
            $result
        );
    }

    #[Test]
    public function searchWordIsSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function synonymsAreSetBySettings(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function categoriesAreSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function featuresAreSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function regionIsSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function regionsAreSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function startIsSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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
            DateTimeImmutable::class,
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

    #[Test]
    public function endIsSetByRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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
            DateTimeImmutable::class,
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

    #[DataProvider('possibleEndAndStartNullCombinations')]
    #[Test]
    public function returnsEndsOnSameDayIfAnyIsNull(
        string $start,
        string $end
    ): void {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    public static function possibleEndAndStartNullCombinations(): Generator
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

    #[Test]
    public function returnsEndsOnSameDayIfBothAreOnSameDay(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[Test]
    public function returnsEndsOnSameDayIfBothAreOnDifferentDays(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    #[DataProvider('possibleSubmittedHighlights')]
    #[Test]
    public function returnsHighlightIfSet(mixed $highlight): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    public static function possibleSubmittedHighlights(): Generator
    {
        yield 'true' => ['highlight' => true];
        yield '1 as integer' => ['highlight' => 1];
        yield '1 as string' => ['highlight' => '1'];
    }

    #[DataProvider('possibleSubmittedFalsyHighlights')]
    #[Test]
    public function returnsNoHighlightIfNotSet(mixed $highlight): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

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

    public static function possibleSubmittedFalsyHighlights(): Generator
    {
        yield 'false' => ['highlight' => false];
        yield '0 as integer' => ['highlight' => 0];
        yield '0 as string' => ['highlight' => '0'];
        yield 'empty string' => ['highlight' => ''];
    }

    #[Test]
    public function returnsOrganizersFromSettings(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->fromSettings([
            'organizers' => '10, ,0, 2,',
        ]);

        self::assertSame([10, 0, 2], $result->getOrganizers());
    }

    #[Test]
    public function returnsOrganizersFromRequest(): void
    {
        $typoScriptService = self::createStub(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService
        );
        $result = $subject->createFromRequestValues(
            [
                'organizers' => [10, 0, 2],
            ],
            [
            ]
        );

        self::assertSame([10, 0, 2], $result->getOrganizers());
    }
}

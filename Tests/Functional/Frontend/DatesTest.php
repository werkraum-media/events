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

namespace Wrm\Events\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Wrm\Events\Frontend\Daest;

/**
 * @covers \Wrm\Events\Frontend\Daest
 */
class DatesTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/events',
    ];

    protected $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected $pathsToProvideInTestInstance = [
        'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:events/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/Rendering.typoscript'
            ],
        ]);
    }

    /**
     * Covers issue https://redmine.werkraum-media.de/issues/10075.
     * Editors can disable events. Dates will still be available.
     * Dates don't make any sense without an event, as they not even have a name.
     *
     * They therefore should not be fetched from persistence.
     *
     * @test
     */
    public function returnsOnlyDatesWithAvailableEventByDemand(): void
    {
        $this->importCSVDataSet(__DIR__ . '/DatesTestFixtures/ReturnsOnlyDatesWithAvailableEventByDemand.csv');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringNotContainsString('Event 1 hidden', $html);
        self::assertStringContainsString('Event 2 visible', $html);
    }

    /**
     * @test
     */
    public function returnsDateAfterStart(): void
    {
        $this->importCSVDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.csv');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][start]' => '2023-02-16',
        ]);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringNotContainsString('Event 1', $html);
        self::assertStringNotContainsString('Event 2', $html);
        self::assertStringContainsString('Event 3', $html);
        self::assertStringContainsString('Event 4', $html);
        self::assertStringContainsString('Event 5', $html);
        self::assertStringContainsString('Event 6', $html);
        self::assertStringContainsString('Event 7', $html);
        self::assertStringContainsString('Event 8', $html);
        self::assertStringContainsString('Event 9', $html);
    }

    /**
     * @test
     */
    public function returnsDateBeforeEnd(): void
    {
        $this->importCSVDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.csv');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][end]' => '2023-02-17',
        ]);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringContainsString('Event 1', $html);
        self::assertStringContainsString('Event 2', $html);
        self::assertStringNotContainsString('Event 3', $html);
        self::assertStringNotContainsString('Event 4', $html);
        self::assertStringContainsString('Event 5', $html);
        self::assertStringContainsString('Event 6', $html);
        self::assertStringContainsString('Event 7', $html);
        self::assertStringContainsString('Event 8', $html);
        self::assertStringContainsString('Event 9', $html);
    }

    /**
     * Covers issue https://redmine.werkraum-media.de/issues/10350.
     * A date can span multiple dates.
     * The visitor might search a time frame within the spaned dates and expects the date to be shown.
     *
     * @test
     */
    public function returnsDateWithinTimeSpan(): void
    {
        $this->importCSVDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.csv');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][start]' => '2023-02-16',
            'events_search[search][end]' => '2023-02-17',
        ]);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringNotContainsString('Event 1', $html);
        self::assertStringNotContainsString('Event 2', $html);
        self::assertStringNotContainsString('Event 3', $html);
        self::assertStringNotContainsString('Event 4', $html);
        self::assertStringContainsString('Event 5', $html);
        self::assertStringContainsString('Event 6', $html);
        self::assertStringContainsString('Event 7', $html);
        self::assertStringContainsString('Event 8', $html);
        self::assertStringContainsString('Event 9', $html);
    }
}

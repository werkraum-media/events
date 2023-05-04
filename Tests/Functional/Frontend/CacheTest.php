<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
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

use Codappix\Typo3PhpDatasets\PhpDataSet;
use DateTimeImmutable;
use DateTimeZone;
use Psr\Http\Message\ResponseInterface;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Wrm\Events\Caching\PageCacheTimeout
 */
class CacheTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/events',
        'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/example',
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
        (new PhpDataSet())->import(['tt_content' => [[
            'uid' => '1',
            'pid' => '1',
            'CType' => 'list',
            'list_type' => 'events_datelisttest',
            'header' => 'All Dates',
        ]]]);
        $this->setUpFrontendRootPage(1, $this->getTypoScriptFiles());
    }

    /**
     * @test
     */
    public function setupReturnsSystemDefaults(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('max-age=86400', $response->getHeaderLine('Cache-Control'));
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    /**
     * @test
     */
    public function setupReturnsDefaultsIfEventsEndLater(): void
    {
        (new PhpDataSet())->import([
            'tx_events_domain_model_event' => [
                [
                    'uid' => '1',
                    'title' => 'Test Event 1',
                ],
            ],
            'tx_events_domain_model_date' => [
                [
                    'event' => '1',
                    'start' => time(),
                    'end' => time() + 86400 + 50,
                ],
            ],
        ]);

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('max-age=86400', $response->getHeaderLine('Cache-Control'));
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    /**
     * @test
     */
    public function setupReturnsEarlierIfEventsChangeBeforeSystemDefault(): void
    {
        (new PhpDataSet())->import([
            'tx_events_domain_model_event' => [
                [
                    'uid' => '1',
                    'pid' => '2',
                    'title' => 'Test Event 1',
                ],
            ],
            'tx_events_domain_model_date' => [
                [
                    'pid' => '2',
                    'event' => '1',
                    'start' => time(),
                    'end' => time() + 50,
                ],
            ],
        ]);

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertMaxAge(50, $response);
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    /**
     * @test
     */
    public function setupReturnsMidnightIfConfigured(): void
    {
        (new PhpDataSet())->import([
            'tx_events_domain_model_event' => [
                [
                    'uid' => '1',
                    'pid' => '2',
                    'title' => 'Test Event 1',
                ],
            ],
            'tx_events_domain_model_date' => [
                [
                    'pid' => '2',
                    'event' => '1',
                    'start' => time(),
                    'end' => time() + 50,
                ],
            ],
        ]);

        $this->setUpFrontendRootPage(1, array_merge_recursive($this->getTypoScriptFiles(), [
            'setup' => [
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/CachingMidnight.typoscript'
            ],
        ]));

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $midnight = (int) (new DateTimeImmutable('tomorrow midnight', new DateTimeZone('UTC')))->format('U');
        $age = $midnight - time();
        self::assertMaxAge($age, $response);
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    private static function assertMaxAge(int $age, ResponseInterface $response): void
    {
        [$prefix, $value] = explode('=', $response->getHeaderLine('Cache-Control'));

        self::assertSame('max-age', $prefix);

        // We might be one sec off due to how fast code is executed, so add a small offset
        $value = (int)$value;
        self::assertLessThanOrEqual($age + 1, $value, 'Max age of cached response is higher than expected.');
        self::assertGreaterThanOrEqual($age - 1, $value, 'Max age of cached response is less than expected.');
    }

    private function getTypoScriptFiles(): array
    {
        return [
            'constants' => [
                'EXT:events/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/Rendering.typoscript'
            ],
        ];
    }
}

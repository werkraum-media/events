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

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use Codappix\Typo3PhpDatasets\PhpDataSet;
use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class CacheTest extends AbstractFrontendTestCase
{
    const HTTP_DATE_FORMAT_RFC7231 = 'D, d M Y H:i:s \\G\\M\\T';

    protected function setUp(): void
    {
        $this->configurationToUseInTestInstance = [
            'SYS' => [
                // Combined with flushCaches.
                // Ensures that we have expected TYPO3 caching within each test.
                // But we don't keep caches from one test to the other.
                'caching' => [
                    'cacheConfigurations' => [
                        'pages' => [
                            'backend' => SimpleFileBackend::class,
                            'options' => [
                                'compression' => '__UNSET',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        parent::setUp();

        $this->get(CacheManager::class)->flushCaches();

        (new PhpDataSet())->import(['tt_content' => [[
            'uid' => '1',
            'pid' => '1',
            'CType' => 'events_datelisttest',
            'header' => 'All Dates',
        ]]]);
    }

    #[Test]
    public function returnsSystemDefaults(): void
    {
        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep());

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('max-age=86400', $response->getHeaderLine('Cache-Control'));
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    #[Test]
    public function returnsDefaultsIfEventsEndLater(): void
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

        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep());

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('max-age=86400', $response->getHeaderLine('Cache-Control'));
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    #[Test]
    public function returnsEarlierIfEventsEndEarlier(): void
    {
        $end = (new DateTimeImmutable('tomorrow midnight', new DateTimeZone('UTC')))->modify('+2 hours');

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
                    'end' => $end->format('U'),
                ],
            ],
        ]);

        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep());

        self::assertSame(200, $response->getStatusCode());
        self::assertCacheHeaders($end, $response);
    }

    #[Test]
    public function returnsEarlierIfStartEndEalierAndIsUpcoming(): void
    {
        $end = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->modify('+2 hours');

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
                    'start' => $end->format('U'),
                    'end' => '0',
                ],
            ],
        ]);

        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep([
            'plugin.' => [
                'tx_events.' => [
                    'settings.' => [
                        'upcoming' => 1,
                    ],
                ],
            ],
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertCacheHeaders($end, $response);
    }

    #[Test]
    public function usesEarliestTimeout(): void
    {
        $end = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->modify('+2 hours');

        (new PhpDataSet())->import([
            'tx_events_domain_model_event' => [
                [
                    'uid' => '1',
                    'pid' => '2',
                    'title' => 'Test Event 1',
                ],
                [
                    'uid' => '2',
                    'pid' => '2',
                    'title' => 'Test Event 2',
                ],
            ],
            'tx_events_domain_model_date' => [
                [
                    'pid' => '2',
                    'event' => '1',
                    'start' => $end->format('U'),
                    'end' => '0',
                ],
                [
                    'pid' => '2',
                    'event' => '2',
                    'start' => $end->modify('+2 hours')->format('U'),
                    'end' => '0',
                ],
            ],
        ]);

        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep([
            'plugin.' => [
                'tx_events.' => [
                    'settings.' => [
                        'upcoming' => 1,
                    ],
                ],
            ],
        ]));

        self::assertSame(200, $response->getStatusCode());
        self::assertCacheHeaders($end, $response);
    }

    #[Test]
    public function returnsMidnightIfConfigured(): void
    {
        $midnight = (new DateTimeImmutable('tomorrow midnight', new DateTimeZone('UTC')));

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
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/CachingMidnight.typoscript',
            ],
        ]));

        $response = $this->executeFrontendSubRequest($this->getRequestWithSleep());

        self::assertSame(200, $response->getStatusCode());
        self::assertCacheHeaders($midnight, $response);
        self::assertSame('public', $response->getHeaderLine('Pragma'));
    }

    #[Test]
    public function cachesAreClearedByImport(): void
    {
        // Assert frontend is cached
        $this->assertResponseIsNotCached($this->executeFrontendSubRequest($this->getRequestWithSleep()));
        $this->assertResponseIsCached($this->executeFrontendSubRequest($this->getRequestWithSleep()));

        // Import
        $this->importPHPDataSet(__DIR__ . '/../Import/DestinationDataTest/Fixtures/Database/DefaultImportConfiguration.php');
        $this->setUpResponses([
            new Response(200, [], file_get_contents(__DIR__ . '/../Import/DestinationDataTest/Fixtures/ResponseWithSingleImageForSingleEvent.json') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/../Import/DestinationDataTest/Fixtures/ExampleImage.jpg') ?: ''),
        ]);
        $this->executeCommand();

        // Assert frontend is not cached on first hit
        $this->setUpFrontendRendering();
        $this->assertResponseIsNotCached($this->executeFrontendSubRequest($this->getRequestWithSleep()));
        $this->assertResponseIsCached($this->executeFrontendSubRequest($this->getRequestWithSleep()));
    }

    private static function assertCacheHeaders(DateTimeImmutable $end, ResponseInterface $response): void
    {
        self::assertSame('public', $response->getHeaderLine('Pragma'));

        $expectedExpires = $end
            ->setTimezone(new DateTimeZone('GMT'))
            ->format(self::HTTP_DATE_FORMAT_RFC7231)
        ;
        self::assertSame($expectedExpires, $response->getHeaderLine('Expires'));

        [$prefix, $value] = explode('=', $response->getHeaderLine('Cache-Control'));
        self::assertSame('max-age', $prefix);

        // We might be seconds off due to our created offset within the rendering.
        $value = (int)$value;
        $age = ((int)$end->format('U')) - time();
        self::assertLessThanOrEqual($age + 4, $value, 'Max age of cached response is higher than expected.');
        self::assertGreaterThanOrEqual($age - 3, $value, 'Max age of cached response is less than expected.');
    }

    private function assertResponseIsNotCached(ResponseInterface $response): void
    {
        if ((new Typo3Version())->getMajorVersion() < 11) {
            self::assertStringNotContainsString('Cached page', $response->getBody()->__toString());
            return;
        }
        self::assertStringStartsNotWith('Cached page', $response->getHeaderLine('X-TYPO3-Debug-Cache'));
    }

    private function assertResponseIsCached(ResponseInterface $response): void
    {
        self::assertStringStartsWith('Cached page', $response->getHeaderLine('X-TYPO3-Debug-Cache'));
    }

    private function getRequestWithSleep(array $typoScript = []): InternalRequest
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withInstructions([
            $this->getTypoScriptInstruction()
                ->withTypoScript(array_merge_recursive($typoScript, [
                    'page.' => [
                        '30.' => [
                            'userFunc.' => [
                                'sleep' => '2',
                            ],
                        ],
                    ],
                ])),
        ]);

        return $request;
    }
}

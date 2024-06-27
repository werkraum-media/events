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

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use WerkraumMedia\Events\Frontend\Dates;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

class DatesTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'seo',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }

    /**
     * Covers issue https://redmine.werkraum-media.de/issues/10075.
     * Editors can disable events. Dates will still be available.
     * Dates don't make any sense without an event, as they not even have a name.
     *
     * They therefore should not be fetched from persistence.
     */
    #[Test]
    public function returnsOnlyDatesWithAvailableEventByDemand(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/ReturnsOnlyDatesWithAvailableEventByDemand.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringNotContainsString('Event 1 hidden', $html);
        self::assertStringContainsString('Event 2 visible', $html);
    }

    #[Test]
    public function returnsDateAfterStart(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][start]' => '2023-02-16',
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

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

    #[Test]
    public function returnsDateBeforeEnd(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][end]' => '2023-02-17',
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

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
     */
    #[Test]
    public function returnsDateWithinTimeSpan(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/ReturnsDateWithinTimeSpan.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withQueryParameters([
            'events_search[search][start]' => '2023-02-16',
            'events_search[search][end]' => '2023-02-17',
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

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

    #[Test]
    public function returns404IfEventIsHidden(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/Returns404IfEventIsHidden.php');

        $response = $this->issueDetailRequest();

        self::assertSame(404, $response->getStatusCode());
    }

    #[Test]
    public function returnsUpcomingDates(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/ReturnsUpcomingDates.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withInstructions([
            $this->getTypoScriptInstruction()
                ->withTypoScript([
                    'plugin.' => [
                        'tx_events.' => [
                            'settings.' => [
                                'upcoming' => '1',
                            ],
                        ],
                    ],
                ]),
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringNotContainsString('Event 1', $html);
        self::assertStringContainsString('Event 2', $html);
    }

    #[Test]
    public function addsMetaTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/DateMetaTags.php');

        $response = $this->issueDetailRequest();

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta name="description" content="Teaser of Event">', $html);
        self::assertStringContainsString('<meta name="keywords" content="Gewölbe, Goethe, Horst Damm, Kästner, Theater">', $html);
    }

    #[Test]
    public function addsOpenGraphTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/DateOpenGraphTags.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta property="og:title" content="Title of Event 15.02.2023 00:00">', $html);
        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
        self::assertStringContainsString('<meta property="og:image" content="http://example.com/fileadmin/user_uploads/example-for-event.gif">', $html);
    }

    #[Test]
    public function addsSocialMediaTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/DateSocialMediaTags.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta name="twitter:card" content="summary">', $html);
        self::assertStringContainsString('<meta name="twitter:title" content="Title of Event 15.02.2023 00:00">', $html);
        self::assertStringContainsString('<meta name="twitter:description" content="Teaser of Event">', $html);
        self::assertStringContainsString('<meta name="twitter:image" content="http://example.com/fileadmin/user_uploads/example-for-event.gif">', $html);
    }

    #[Test]
    public function altersPageTitle(): void
    {
        $this->importPHPDataSet(__DIR__ . '/DatesTestFixtures/DatePageTitle.php');

        $response = $this->issueDetailRequest();

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<title>Title of Event 15.02.2023 00:00</title>', $html);
    }

    private function issueDetailRequest(): ResponseInterface
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_events_dateshow[date]', '1');

        return $this->executeFrontendSubRequest($request);
    }
}

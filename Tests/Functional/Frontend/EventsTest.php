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

use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

class EventsTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/example',
        ];
        $this->coreExtensionsToLoad = [
            'seo',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }

    #[Test]
    public function addsMetaTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/EventsTestFixtures/EventMetaTags.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta name="description" content="Teaser of Event">', $html);
        self::assertStringContainsString('<meta name="keywords" content="Gewölbe, Goethe, Horst Damm, Kästner, Theater">', $html);
    }

    #[Test]
    public function addsOpenGraphTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/EventsTestFixtures/EventOpenGraphTags.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta property="og:title" content="Title of Event">', $html);
        self::assertStringContainsString('<meta property="og:type" content="website">', $html);
        self::assertStringContainsString('<meta property="og:image" content="http://example.com/fileadmin/user_uploads/example-for-event.gif">', $html);
    }

    #[Test]
    public function addsSocialMediaTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/EventsTestFixtures/EventSocialMediaTags.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta name="twitter:card" content="summary">', $html);
        self::assertStringContainsString('<meta name="twitter:title" content="Title of Event">', $html);
        self::assertStringContainsString('<meta name="twitter:description" content="Teaser of Event">', $html);
        self::assertStringContainsString('<meta name="twitter:image" content="http://example.com/fileadmin/user_uploads/example-for-event.gif">', $html);
    }

    #[Test]
    public function altersPageTitle(): void
    {
        $this->importPHPDataSet(__DIR__ . '/EventsTestFixtures/EventPageTitle.php');

        $response = $this->issueDetailRequest();
        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<title>Title of Event</title>', $html);
    }

    private function issueDetailRequest(): ResponseInterface
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_events_eventshow[event]', '1');

        return $this->executeFrontendSubRequest($request);
    }
}

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
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

class EventsTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/example',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }

    #[Test]
    public function addsMetaTags(): void
    {
        $this->importPHPDataSet(__DIR__ . '/EventsTestFixtures/EventMetaTags.php');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_events_eventshow[event]', '1');
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('<meta name="description" content="Teaser of Event" />', $html);
        self::assertStringContainsString('<meta name="keywords" content="Gewölbe, Goethe, Horst Damm, Kästner, Theater" />', $html);
    }
}

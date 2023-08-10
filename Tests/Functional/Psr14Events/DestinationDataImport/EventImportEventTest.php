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

namespace Wrm\Events\Tests\Functional\Psr14Events\DestinationDataImport;

use GuzzleHttp\Psr7\Response;
use Wrm\Events\Tests\Functional\Import\DestinationDataTest\AbstractTest;

final class EventImportEventTest extends AbstractTest
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'typo3conf/ext/events/Tests/Functional/Psr14Events/DestinationDataImport/Fixtures/Extensions/custom_event';

        parent::setUp();

        $this->setUpConfiguration([
            'restUrl = https://example.com/some-path/',
            'license = example-license',
            'restType = Event',
            'restLimit = 3',
            'restMode = next_months,12',
            'restTemplate = ET2014A.json',
        ]);
    }

    /**
     * @test
     */
    public function registeredEventHandlerCanModifyEvent(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/RegisteredEventHandlerCanModifyEvent.php');
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Responses/RegisteredEventHandlerCanModifyEvent.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/RegisteredEventHandlerCanModifyEvent.php');
    }
}

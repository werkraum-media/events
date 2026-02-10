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

namespace WerkraumMedia\Events\Tests\Functional\Psr14Events\DestinationDataImport;

use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest\AbstractTestCase;

final class EventImportEventTest extends AbstractTestCase
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

    #[Test]
    public function registeredEventHandlerCanModifyEvent(): void
    {
        $this->setDateAspect(new DateTimeImmutable('2021-07-13', new DateTimeZone('Europe/Berlin')));

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/RegisteredEventHandlerCanModifyEvent.php');
        $this->setUpResponses([new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/Responses/RegisteredEventHandlerCanModifyEvent.json') ?: '')]);

        $this->executeCommand();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/RegisteredEventHandlerCanModifyEvent.php');
    }
}

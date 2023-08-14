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

namespace Wrm\Events\Service\DestinationDataImportService\Events;

use Wrm\Events\Domain\Model\Event;

final class EventImportEvent
{
    /**
     * @var Event
     */
    private $existingEvent;

    /**
     * @var Event
     */
    private $eventToImport;

    /**
     * @var array
     */
    private $eventData;

    public function __construct(
        Event $existingEvent,
        Event $eventToImport,
        array $eventData
    ) {
        $this->existingEvent = $existingEvent;
        $this->eventToImport = $eventToImport;
        $this->eventData = $eventData;
    }

    /**
     * The existing event, or newly created, prior applying modifications.
     * Can be used to compare existing data with new data for import.
     */
    public function getBaseEvent(): Event
    {
        return clone $this->existingEvent;
    }

    /**
     * The object that will finally be imported.
     * Modifications to this object will result in modifications of imported data.
     */
    public function getEventToImport(): Event
    {
        return $this->eventToImport;
    }

    /**
     * The original data as received from API.
     */
    public function getEventData(): array
    {
        return $this->eventData;
    }
}

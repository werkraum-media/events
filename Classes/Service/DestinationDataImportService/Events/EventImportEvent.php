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

    public function __construct(
        Event $existingEvent,
        Event $eventToImport
    ) {
        $this->existingEvent = $existingEvent;
        $this->eventToImport = $eventToImport;
    }

    public function getBaseEvent(): Event
    {
        return clone $this->existingEvent;
    }

    public function getEventToImport(): Event
    {
        return $this->eventToImport;
    }
}

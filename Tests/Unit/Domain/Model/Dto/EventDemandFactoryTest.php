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

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Dto\EventDemand;
use WerkraumMedia\Events\Domain\Model\Dto\EventDemandFactory;

class EventDemandFactoryTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new EventDemandFactory();

        self::assertInstanceOf(
            EventDemandFactory::class,
            $subject
        );
    }

    #[Test]
    public function fromSettingsDoesNotThrowUndefinedArrayKeyWarnings(): void
    {
        $subject = new EventDemandFactory();

        $result = $subject->fromSettings([]);

        self::assertInstanceOf(
            EventDemand::class,
            $result
        );
    }
}

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

namespace WerkraumMedia\Events\Tests\Functional\Updates;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;
use WerkraumMedia\Events\Updates\MigrateDuplicateLocations;

#[TestDox('The update wizard to migrate duplicate locations')]
final class MigrateDuplicateLocationsTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = $this->get(MigrateDuplicateLocations::class);

        self::assertInstanceOf(MigrateDuplicateLocations::class, $subject);
    }

    #[Test]
    public function keepsDataIfNothingToDo(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/MigrateDuplicateLocationsNoDuplicates.php');

        $subject = $this->get(MigrateDuplicateLocations::class);

        self::assertInstanceOf(MigrateDuplicateLocations::class, $subject);
        self::assertTrue($subject->updateNecessary());

        $this->assertPHPDataSet(__DIR__ . '/Fixtures/MigrateDuplicateLocationsNoDuplicates.php');
    }

    #[Test]
    public function migratesDuplicateEntries(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/MigrateDuplicateLocations.php');

        $subject = $this->get(MigrateDuplicateLocations::class);

        self::assertInstanceOf(MigrateDuplicateLocations::class, $subject);
        self::assertTrue($subject->updateNecessary());
        $subject->executeUpdate();

        $this->assertPHPDataSet(__DIR__ . '/Assertions/MigrateDuplicateLocations.php');
    }
}

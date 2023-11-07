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

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Location;

final class LocationTest extends TestCase
{
    #[DataProvider('possibleLatitudeAndLongitude')]
    #[Test]
    public function normalizesLatitudeAndLongitude(
        string $latitude,
        string $longitude
    ): void {
        $subject = new Location(
            'Name',
            'Street',
            'Zip',
            'City',
            'District',
            'Country',
            'Phone',
            $latitude,
            $longitude,
            1
        );

        self::assertSame(
            '50.720971',
            $subject->getLatitude()
        );
        self::assertSame(
            '11.335230',
            $subject->getLongitude()
        );
    }

    public static function possibleLatitudeAndLongitude(): array
    {
        return [
            'Using float numbers' => [
                'latitude' => (string)50.720971023258805,
                'longitude' => (string)11.335229873657227,
            ],
            'Using ","' => [
                'latitude' => '50,720971023258805',
                'longitude' => '11,335229873657227',
            ],
            'Using "."' => [
                'latitude' => '50.720971023258805',
                'longitude' => '11.335229873657227',
            ],
        ];
    }
}

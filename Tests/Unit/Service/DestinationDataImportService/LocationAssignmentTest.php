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

namespace WerkraumMedia\Events\Tests\Unit\Service\DestinationDataImportService;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Location;
use WerkraumMedia\Events\Domain\Repository\LocationRepository;
use WerkraumMedia\Events\Service\DestinationDataImportService\LocationAssignment;

final class LocationAssignmentTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();
    }

    #[Test]
    public function canBeCreated(): void
    {
        $repository = self::createStub(LocationRepository::class);
        $subject = new LocationAssignment(
            $repository
        );

        self::assertInstanceOf(
            LocationAssignment::class,
            $subject
        );
    }

    /**
     * @param string|float $latitude
     * @param string|float $longitude
     */
    #[DataProvider('possibleLatitudeAndLongitude')]
    #[Test]
    public function normalizesLatitudeAndLongitude(
        $latitude,
        $longitude
    ): void {
        $repository = self::createStub(LocationRepository::class);
        $repository->method('findOneByGlobalId')->willReturn(null);

        $subject = new LocationAssignment(
            $repository
        );

        $result = $subject->getLocation([
            'name' => 'Name',
            'street' => 'Street',
            'zip' => 'Zip',
            'city' => 'City',
            'district' => 'District',
            'country' => 'Country',
            'phone' => 'Phone',
            'geo' => [
                'main' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
            ],
        ]);

        self::assertInstanceOf(
            Location::class,
            $result
        );
        self::assertSame(
            '50.720971',
            $result->getLatitude(),
            'Latitude returns unexpected value.'
        );
        self::assertSame(
            '11.335230',
            $result->getLongitude(),
            'Longitude returns unexpected value.'
        );
    }

    public static function possibleLatitudeAndLongitude(): array
    {
        return [
            'Using float numbers' => [
                'latitude' => 50.720971023258805,
                'longitude' => 11.335229873657227,
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

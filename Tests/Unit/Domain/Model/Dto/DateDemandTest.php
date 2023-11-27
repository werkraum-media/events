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

namespace WerkraumMedia\Events\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;

final class DateDemandTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new DateDemand();

        self::assertInstanceOf(
            DateDemand::class,
            $subject
        );
    }

    #[Test]
    #[DataProvider('possibleFeatures')]
    public function setsFeatures(
        array $incoming,
        array $expected
    ): void {
        $subject = new DateDemand();

        $subject->setFeatures($incoming);

        self::assertSame($expected, $subject->getFeatures());
    }

    public static function possibleFeatures(): array
    {
        return [
            'Empty' => [
                'incoming'=> [],
                'expected'=> [],
            ],
            'Single empty entry' => [
                'incoming'=> [''],
                'expected'=> [],
            ],
            'Single entry' => [
                'incoming'=> ['10'],
                'expected'=> [10],
            ],
            'Two entries' => [
                'incoming'=> ['10', '20'],
                'expected'=> [10, 20],
            ],
            'Two entries, one empty' => [
                'incoming'=> ['10', ''],
                'expected'=> [10],
            ],
        ];
    }
}

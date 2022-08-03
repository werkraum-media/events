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

namespace Wrm\Events\Tests\Unit\Domain\Model\Dto;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Dto\DateDemandFactory;
use Wrm\Events\Tests\ProphecyTrait;

/**
 * @covers \Wrm\Events\Domain\Model\Dto\DateDemandFactory
 */
class DateDemandFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $typoScriptService = $this->prophesize(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService->reveal()
        );

        self::assertInstanceOf(
            DateDemandFactory::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function fromSettingsDoesNotThrowUndefinedArrayKeyWarnings(): void
    {
        $typoScriptService = $this->prophesize(TypoScriptService::class);

        $subject = new DateDemandFactory(
            $typoScriptService->reveal()
        );

        $result = $subject->fromSettings([]);

        self::assertInstanceOf(
            DateDemand::class,
            $result
        );
    }
}

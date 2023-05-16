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

namespace Wrm\Events\Tests\Functional;

use Codappix\Typo3PhpDatasets\TestingFramework;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractFunctionalTestCase extends FunctionalTestCase
{
    use TestingFramework;

    protected function setUp(): void
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->configurationToUseInTestInstance, [
            'GFX' => [
                'processor_enabled' => true,
                'processor_path' => '/usr/bin/',
                'processor_path_lzw' => '/usr/bin/',
                'processor' => 'ImageMagick',
            ],
        ]);

        parent::setUp();
    }
}

<?php

declare(strict_types=1);

/*
 * Copyright (C) 2024 Daniel Siepmann <daniel.siepmann@codappix.com>
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

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractFrontendTestCase extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'seo',
        ];

        $this->testExtensionsToLoad = [
            ...$this->testExtensionsToLoad,
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/example/',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }
}

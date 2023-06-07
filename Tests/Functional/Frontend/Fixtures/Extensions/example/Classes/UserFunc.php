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

namespace Wrm\EventsExample;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class UserFunc
{
    /**
     * @var ContentObjectRenderer
     */
    public $cObj;

    public function accessTsfeTimeout(): string
    {
        return 'get_cache_timeout: ' . $this->getTsfe()->get_cache_timeout();
    }

    public function sleep(string $content, array $configuration): string
    {
        $sleep = (int)$this->cObj->stdWrapValue('sleep', $configuration['userFunc.'], 0);
        sleep($sleep);
        return 'Sleep for ' . $sleep . ' seconds';
    }

    public function getTsfe(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}

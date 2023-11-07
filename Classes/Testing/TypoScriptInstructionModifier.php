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

namespace WerkraumMedia\Events\Testing;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\Hook\TypoScriptInstructionModifier as Typo3TypoScriptInstructionModifier;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\RequestBootstrap;

/**
 * Wrap original code to check whether internal request exists.
 * It does not exist during import.
 *
 * No PR upstream as this is probably an unusual use case.
 * But we call frontend, import, frontend so have a mix.
 */
class TypoScriptInstructionModifier implements SingletonInterface
{
    public function apply(array $parameters, TemplateService $service): void
    {
        $request = RequestBootstrap::getInternalRequest();
        if ($request === null) {
            return;
        }

        GeneralUtility::callUserFunction(
            Typo3TypoScriptInstructionModifier::class . '->apply',
            $parameters,
            $service
        );
    }
}

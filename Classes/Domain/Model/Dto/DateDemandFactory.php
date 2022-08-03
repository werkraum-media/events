<?php

namespace Wrm\Events\Domain\Model\Dto;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class DateDemandFactory
{
    /**
     * @var TypoScriptService
     */
    private $typoScriptService;

    /**
     * @var contentObjectRenderer
     */
    private $contentObjectRenderer;

    public function __construct(
        TypoScriptService $typoScriptService
    ) {
        $this->typoScriptService = $typoScriptService;
    }

    public function setContentObjectRenderer(
        ContentObjectRenderer $contentObjectRenderer
    ): void {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }

    public function fromSettings(array $settings): DateDemand
    {
        $demand = new DateDemand();

        if ($this->contentObjectRenderer instanceof ContentObjectRenderer) {
            $typoScriptSettings = $this->typoScriptService->convertPlainArrayToTypoScriptArray($settings);
            foreach (array_keys($settings) as $settingName) {
                $settings[$settingName] = $this->contentObjectRenderer->stdWrapValue($settingName, $typoScriptSettings, '');
            }
        }

        if (!empty($settings['region'])) {
            $demand->setRegions(GeneralUtility::intExplode(',', (string)$settings['region'], true));
        }
        if (!empty($settings['locations'])) {
            $demand->setLocations(GeneralUtility::intExplode(',', (string)$settings['locations'], true));
        }
        if (!empty($settings['categories'])) {
            $demand->setCategories((string)$settings['categories']);
        }
        $categoryCombination = 'and';
        if (isset($settings['categoryCombination']) && (int)$settings['categoryCombination'] === 1) {
            $categoryCombination = 'or';
        }
        $demand->setCategoryCombination($categoryCombination);

        if (isset($settings['includeSubcategories'])) {
            $demand->setIncludeSubCategories((bool)$settings['includeSubcategories']);
        }
        if (isset($settings['sortByDate'])) {
            $demand->setSortBy((string)$settings['sortByDate']);
        }
        if (!empty($settings['sortOrder'])) {
            $demand->setSortOrder((string)$settings['sortOrder']);
        }
        if (isset($settings['highlight'])) {
            $demand->setHighlight((bool)$settings['highlight']);
        }
        if (!empty($settings['start'])) {
            $demand->setStart((int)$settings['start']);
        }
        if (!empty($settings['end'])) {
            $demand->setEnd((int)$settings['end']);
        }
        if (isset($settings['useMidnight'])) {
            $demand->setUseMidnight((bool)$settings['useMidnight']);
        }
        if (!empty($settings['limit'])) {
            $demand->setLimit($settings['limit']);
        }
        if (!empty($settings['queryCallback'])) {
            $demand->setQueryCallback($settings['queryCallback']);
        }

        return $demand;
    }
}

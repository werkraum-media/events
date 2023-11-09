<?php

namespace WerkraumMedia\Events\Domain\Model\Dto;

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
        if (!empty($settings['organizers'])) {
            $demand->setOrganizers(GeneralUtility::intExplode(',', (string)$settings['organizers'], true));
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
        if (isset($settings['upcoming'])) {
            $demand->setUpcoming((bool)$settings['upcoming']);
        }
        if (!empty($settings['limit'])) {
            $demand->setLimit($settings['limit']);
        }
        if (!empty($settings['queryCallback'])) {
            $demand->setQueryCallback($settings['queryCallback']);
        }

        return $demand;
    }

    public function createFromRequestValues(
        array $submittedValues,
        array $settings
    ): DateDemand {
        $instance = new DateDemand();
        $instance->setSearchword($submittedValues['searchword'] ?? '');
        $instance->setSynonyms($settings['synonyms'] ?? []);

        if (isset($submittedValues['locations']) && is_array($submittedValues['locations'])) {
            $instance->setLocations($submittedValues['locations']);
        }

        if (isset($submittedValues['organizers']) && is_array($submittedValues['organizers'])) {
            $instance->setOrganizers($submittedValues['organizers']);
        }

        $instance->setRegions(GeneralUtility::intExplode(',', $submittedValues['region'] ?? '', true));
        if (isset($submittedValues['regions']) && is_array($submittedValues['regions'])) {
            $instance->setRegions($submittedValues['regions']);
        }

        if ($submittedValues['highlight'] ?? false) {
            $instance->setHighlight(true);
        }

        if (isset($submittedValues['start']) && $submittedValues['start'] !== '') {
            $instance->setStart(strtotime($submittedValues['start'] . ' 00:00') ?: null);
        }
        if (isset($submittedValues['end']) && $submittedValues['end'] !== '') {
            $instance->setEnd(strtotime($submittedValues['end'] . ' 23:59') ?: null);
        }
        if (isset($submittedValues['considerDate']) && $submittedValues['considerDate'] !== '') {
            $instance->setConsiderDate((bool)$submittedValues['considerDate']);
        }

        if (isset($submittedValues['userCategories']) && is_array($submittedValues['userCategories'])) {
            $instance->setUserCategories($submittedValues['userCategories']);
        }

        if (isset($submittedValues['features']) && is_array($submittedValues['features'])) {
            $instance->setFeatures($submittedValues['features']);
        }

        $instance->setSortBy($settings['sortByDate'] ?? '');
        $instance->setSortOrder($settings['sortOrder'] ?? '');
        $instance->setQueryCallback($settings['queryCallback'] ?? '');

        if (!empty($settings['limit'])) {
            $instance->setLimit($settings['limit']);
        }

        return $instance;
    }
}

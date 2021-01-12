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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class EventDemandFactory
{
    public function fromSettings(array $settings): EventDemand
    {
        /** @var EventDemand $demand */
        $demand = GeneralUtility::makeInstance(EventDemand::class);

        $demand->setRegion((string)$settings['region']);

        $demand->setCategories((string)$settings['categories']);

        $categoryCombination = 'and';
        if ((int)$settings['categoryCombination'] === 1) {
            $categoryCombination = 'or';
        }
        $demand->setCategoryCombination($categoryCombination);

        $demand->setIncludeSubCategories((bool)$settings['includeSubcategories']);

        $demand->setSortBy((string)$settings['sortByEvent']);
        $demand->setSortOrder((string)$settings['sortOrder']);

        $demand->setHighlight((bool)$settings['highlight']);

        $demand->setRecordUids(GeneralUtility::intExplode(',', $settings['selectedRecords'], true));

        if (!empty($settings['limit'])) {
            $demand->setLimit($settings['limit']);
        }

        return $demand;
    }
}

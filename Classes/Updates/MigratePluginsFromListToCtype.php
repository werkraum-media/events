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

namespace WerkraumMedia\Events\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

// TODO: typo3/cms-core:14.0 Remove condition as this class is provided since 13.
if (class_exists(AbstractListTypeToCTypeUpdate::class) === false) {
    final class MigratePluginsFromListToCtype
    {
    }
    return;
}

#[UpgradeWizard(MigratePluginsFromListToCtype::class)]
final class MigratePluginsFromListToCtype extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'events_datelist' => 'events_datelist',
            'events_datesearch' => 'events_datesearch',
            'events_dateshow ' => 'events_dateshow',
            'events_selected ' => 'events_selected',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrate EXT:events content elements.';
    }

    public function getDescription(): string
    {
        return 'Migrate CType from list to dedicated plugins.';
    }
}

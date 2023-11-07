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

namespace WerkraumMedia\Events\Service\DestinationDataImportService\Slugger;

/**
 * Defines a slugger for a specific type, e.g. event or date.
 */
interface SluggerType
{
    /**
     * Adjust record prior to slug generation.
     *
     * That way fields used by the generation can be populated.
     *
     * @param string[] $record
     *
     * @return string[]
     */
    public function prepareRecordForSlugGeneration(array $record): array;

    /**
     * Defines the name of the database column that's holding the slug.
     */
    public function getSlugColumn(): string;

    /**
     * Returns the name of the database table that is supported.
     */
    public function getSupportedTableName(): string;
}

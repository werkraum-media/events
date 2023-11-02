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

namespace Wrm\Events\Service\DestinationDataImportService\DataHandler;

use InvalidArgumentException;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

final class Assignment
{
    /**
     * @var int
     */
    private $uid;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var int[]
     */
    private $uids;

    /**
     * @param AbstractDomainObject[] $assignments
     */
    public function __construct(
        int $uid,
        string $columnName,
        array $assignments
    ) {
        $this->uid = $uid;
        $this->columnName = $columnName;
        $this->uids = array_map(static function (AbstractDomainObject $model): int {
            $uid = $model->getUid();
            if (is_int($uid) === false) {
                throw new InvalidArgumentException('Only object with uid supported.', 1698936965);
            }
            return $uid;
        }, $assignments);
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @return int[]
     */
    public function getUids(): array
    {
        return $this->uids;
    }
}

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

namespace Wrm\Events\Caching;

use DateTime;
use DateTimeImmutable;
use TYPO3\CMS\Core\SingletonInterface;
use Wrm\Events\Events\Controller\DateListVariables;

/**
 * Teaches TYPO3 to set proper timeout for page cache.
 *
 * Takes timings of rendered dates into account.
 * Reduced timeout of page cache in case events (dates) might change before already calculated timeout.
 */
class PageCacheTimeout implements SingletonInterface
{
    /**
     * @var int
     */
    private $earliestTimeout = 0;

    public function calculateCacheTimout(
        array $parameters
    ): int {
        $timeout = $parameters['cacheTimeout'];

        if ($this->earliestTimeout <= 0) {
            return $timeout;
        }

        return min($timeout, $this->earliestTimeout);
    }

    public function trackDates(DateListVariables $event): void
    {
        if ($event->getDemand()->shouldShowFromMidnight()) {
            $this->updateTimeout((int) (new DateTimeImmutable('tomorrow midnight'))->format('U'));
            return;
        }

        foreach ($event->getDates() as $date) {
            $endDate = $date->getEnd();
            if (!$endDate instanceof DateTime) {
                continue;
            }

            $this->updateTimeout((int)DateTimeImmutable::createFromMutable($endDate)->format('U'));
        }
    }

    private function updateTimeout(int $timestamp): void
    {
        $newTimeout = $timestamp - time();
        if ($newTimeout <= 0) {
            return;
        }

        if ($this->earliestTimeout === 0) {
            $this->earliestTimeout = $newTimeout;
            return;
        }

        $this->earliestTimeout = min($this->earliestTimeout, $newTimeout);
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['get_cache_timeout']['events'] = self::class . '->calculateCacheTimout';
    }
}

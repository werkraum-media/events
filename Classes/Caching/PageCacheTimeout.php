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

namespace WerkraumMedia\Events\Caching;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\SingletonInterface;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Events\Controller\DateListVariables;

/**
 * Teaches TYPO3 to set proper timeout for page cache.
 *
 * Takes timings of rendered dates into account.
 * Reduced timeout of page cache in case events (dates) might change before already calculated timeout.
 */
class PageCacheTimeout implements SingletonInterface
{
    /**
     * @var DateTimeImmutable|null
     */
    private $timeout;

    /**
     * @var FrontendInterface
     */
    private $runtimeCache;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        CacheManager $cacheManager,
        Context $context
    ) {
        $this->runtimeCache = $cacheManager->getCache('runtime');
        $this->context = $context;
    }

    public function calculateCacheTimout(
        array $parameters
    ): int {
        $typo3Timeout = $parameters['cacheTimeout'];
        $ourTimeout = $this->getTimeout();

        if ($ourTimeout === null) {
            return $typo3Timeout;
        }

        return min($typo3Timeout, $ourTimeout);
    }

    public function trackDates(DateListVariables $event): void
    {
        if ($event->getDemand()->shouldShowFromMidnight()) {
            $this->updateTimeout(new DateTimeImmutable('tomorrow midnight'));
            return;
        }

        if ($event->getDemand()->shouldShowUpcoming()) {
            $this->trackTimeoutByDate($event, static function (Date $date) {
                return $date->getStart();
            });
        }

        $this->trackTimeoutByDate($event, static function (Date $date) {
            return $date->getEnd();
        });
    }

    /**
     * @param callable $callback Receives Date as argument and should return DateTime to use as potential timeout.
     */
    private function trackTimeoutByDate(
        DateListVariables $event,
        callable $callback
    ): void {
        foreach ($event->getDates() as $date) {
            $date = $callback($date);
            if (!$date instanceof DateTime) {
                continue;
            }

            $this->updateTimeout(DateTimeImmutable::createFromMutable($date));
        }
    }

    private function updateTimeout(DateTimeImmutable $newTimeout): void
    {
        $now = $this->getExecution();

        if (
            $newTimeout <= $now
            || (
                $this->timeout instanceof DateTimeImmutable
                && $this->timeout <= $newTimeout
            )
        ) {
            return;
        }

        $this->runtimeCache->remove('core-tslib_fe-get_cache_timeout');
        $this->timeout = $newTimeout;
    }

    private function getTimeout(): ?int
    {
        if (!$this->timeout instanceof DateTimeImmutable) {
            return null;
        }

        return ((int)$this->timeout->format('U')) - ((int)$this->getExecution()->format('U'));
    }

    private function getExecution(): DateTimeImmutable
    {
        $execution = $this->context->getPropertyFromAspect('date', 'full');

        if (!$execution instanceof DateTimeImmutable) {
            throw new InvalidArgumentException('Could not fetch DateTimeImmutable from context.', 1684740576);
        }

        return $execution;
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['get_cache_timeout']['events'] = self::class . '->calculateCacheTimout';
    }
}

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
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Context\Context;
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
     * @var null|DateTimeImmutable
     */
    private $endOfEvent = null;

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
            $this->updateTimeout((new DateTimeImmutable('tomorrow midnight')));
            return;
        }

        foreach ($event->getDates() as $date) {
            $endDate = $date->getEnd();
            if (!$endDate instanceof DateTime) {
                continue;
            }

            $this->updateTimeout(DateTimeImmutable::createFromMutable($endDate));
        }
    }

    private function updateTimeout(DateTimeImmutable $end): void
    {
        $now = new DateTimeImmutable();

        if (
            $end <= $now
            || (
                $this->endOfEvent instanceof DateTimeImmutable
                && $this->endOfEvent >= $end
            )
        ) {
            return;
        }

        $this->runtimeCache->remove('core-tslib_fe-get_cache_timeout');
        $this->endOfEvent = $end;
    }

    private function getTimeout(): ?int
    {
        if (!$this->endOfEvent instanceof DateTimeImmutable) {
            return null;
        }

        $executionTime = $this->context->getPropertyFromAspect('date', 'timestamp');
        return ((int) $this->endOfEvent->format('U')) - $executionTime;
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['get_cache_timeout']['events'] = self::class . '->calculateCacheTimout';
    }
}

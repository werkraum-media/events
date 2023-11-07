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

use TYPO3\CMS\Core\Cache\CacheManager as Typo3CacheManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class CacheManager
{
    private array $tags = [
        'tx_events_domain_model_date',
        'tx_events_domain_model_event',
        'tx_events_domain_model_organizer',
        'tx_events_domain_model_partner',
        'tx_events_domain_model_region',
    ];

    public function __construct(
        private readonly Typo3CacheManager $cacheManager
    ) {
    }

    public function addAllCacheTagsToPage(ContentObjectRenderer $cObject): void
    {
        $cObject->stdWrap_addPageCacheTags('', [
            'addPageCacheTags' => implode(',', $this->tags),
        ]);
    }

    public function clearAllCacheTags(): void
    {
        $this->cacheManager->flushCachesByTags($this->tags);
    }
}

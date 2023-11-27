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

namespace WerkraumMedia\Events\Frontend\MetaInformation;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Frontend\PageTitleProvider\DateTitleProviderInterface;

/**
 * TYPO3 has many different APIs to set meta information like: Page Title, Meta Tags, OpenGraph Tags, etc.
 * Those are combined here for Date detail view.
 * That way there is a single place to connect the details to TYPO3 APIs.
 */
final class DateMetaInformationService implements DateMetaInformationInterface
{
    public function __construct(
        private readonly MetaTagManagerRegistry $metaTagManagerRegistry,
        private readonly DateTitleProviderInterface $titleProvider
    ) {
    }

    public function setDate(Date $date): void
    {
        $this->setDescription($date);
        $this->setKeywords($date);

        $this->titleProvider->setDate($date);
    }

    private function setDescription(Date $date): void
    {
        $description = $date->getEvent()?->getTeaser() ?? '';
        if ($description === '') {
            return;
        }

        $this->metaTagManagerRegistry
            ->getManagerForProperty('description')
            ->addProperty('description', $description)
        ;
    }

    private function setKeywords(Date $date): void
    {
        $keywords = $date->getEvent()?->getKeywords() ?? '';
        if ($keywords === '') {
            return;
        }

        $this->metaTagManagerRegistry
            ->getManagerForProperty('keywords')
            ->addProperty('keywords', $keywords)
        ;
    }
}

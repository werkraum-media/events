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

use TYPO3\CMS\Core\MetaTag\GenericMetaTagManager;
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
        private readonly EventMetaInformationService $eventService,
        private readonly DateTitleProviderInterface $titleProvider
    ) {
    }

    public function setDate(Date $date): void
    {
        $event = $date->getEvent();
        if ($event === null) {
            return;
        }

        // A date mostly sets info based on event, re use existing features.
        $this->eventService->setEvent($event);

        // Now set date specifics.
        $this->titleProvider->setDate($date);

        $this->updateTitles();
    }

    private function updateTitles(): void
    {
        $title = $this->titleProvider->getTitle();
        if ($title === '') {
            return;
        }

        $this->updateOpenGraphTitle($title);
        $this->updateTwitterTitle($title);
    }

    private function updateOpenGraphTitle(string $title): void
    {
        $manager = $this->metaTagManagerRegistry->getManagerForProperty('og:title');
        if ($manager instanceof GenericMetaTagManager) {
            return;
        }

        $manager->removeProperty('og:title');
        $manager->addProperty('og:title', $title);
    }

    private function updateTwitterTitle(string $title): void
    {
        $manager = $this->metaTagManagerRegistry->getManagerForProperty('twitter:title');
        $manager->removeProperty('twitter:title');
        $manager->addProperty('twitter:title', $title);
    }
}

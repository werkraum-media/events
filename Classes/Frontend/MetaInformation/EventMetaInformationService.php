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

namespace Wrm\Events\Frontend\MetaInformation;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Frontend\PageTitleProvider\EventTitleProviderInterface;

/**
 * TYPO3 has many different APIs to set meta information like: Page Title, Meta Tags, OpenGraph Tags, etc.
 * Those are combined here for Event detail view.
 * That way there is a single place to connect the details to TYPO3 APIs.
 */
final class EventMetaInformationService implements EventMetaInformationInterface
{
    /**
     * @var MetaTagManagerRegistry
     */
    private $metaTagManagerRegistry;

    /**
     * @var EventTitleProviderInterface
     */
    private $titleProvider;

    public function __construct(
        MetaTagManagerRegistry $metaTagManagerRegistry,
        EventTitleProviderInterface $titleProvider
    ) {
        $this->metaTagManagerRegistry = $metaTagManagerRegistry;
        $this->titleProvider = $titleProvider;
    }

    public function setEvent(Event $event): void
    {
        $this->setDescription($event);
        $this->setKeywords($event);

        $this->titleProvider->setEvent($event);
    }

    private function setDescription(Event $event): void
    {
        $description = $event->getTeaser();
        if ($description === '') {
            return;
        }

        $this->metaTagManagerRegistry
            ->getManagerForProperty('description')
            ->addProperty('description', $description)
        ;
    }

    private function setKeywords(Event $event): void
    {
        $keywords = $event->getKeywords();
        if ($keywords === '') {
            return;
        }

        $this->metaTagManagerRegistry
            ->getManagerForProperty('keywords')
            ->addProperty('keywords', $keywords)
        ;
    }
}

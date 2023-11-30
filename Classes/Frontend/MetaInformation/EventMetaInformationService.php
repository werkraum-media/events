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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Frontend\PageTitleProvider\EventTitleProviderInterface;

/**
 * TYPO3 has many different APIs to set meta information like: Page Title, Meta Tags, OpenGraph Tags, etc.
 * Those are combined here for Event detail view.
 * That way there is a single place to connect the details to TYPO3 APIs.
 */
final class EventMetaInformationService implements EventMetaInformationInterface
{
    public function __construct(
        private readonly MetaTagManagerRegistry $metaTagManagerRegistry,
        private readonly EventTitleProviderInterface $titleProvider
    ) {
    }

    public function setEvent(Event $event): void
    {
        $this->titleProvider->setEvent($event);

        $this->setDescription($event);
        $this->setKeywords($event);
        $this->setOpenGraphTags($event);
        $this->setSocialMediaTags($event);
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

    private function setOpenGraphTags(Event $event): void
    {
        $tags = array_filter([
            'title' => $this->titleProvider->getTitle(),
            'type' => 'website',
            'image' => $this->getImageUrl($event),
        ]);

        foreach ($tags as $property => $value) {
            $property = 'og:' . $property;
            $manager = $this->metaTagManagerRegistry->getManagerForProperty($property);
            if ($manager instanceof GenericMetaTagManager) {
                continue;
            }
            $manager->addProperty($property, $value);
        }
    }

    private function setSocialMediaTags(Event $event): void
    {
        $title = $this->titleProvider->getTitle();

        $tags = array_filter([
            'twitter:card' => 'summary',
            'twitter:title' => $title,
            'twitter:description' => $event->getTeaser(),
            'twitter:image' => $this->getImageUrl($event),
        ]);

        foreach ($tags as $property => $value) {
            $this->metaTagManagerRegistry
                ->getManagerForProperty($property)
                ->addProperty($property, $value)
            ;
        }
    }

    private function getImageUrl(Event $event): string
    {
        $imageUrl = $event->getImages()[0]?->getOriginalResource()->getPublicUrl() ?? '';
        if ($imageUrl) {
            $imageUrl = GeneralUtility::locationHeaderUrl($imageUrl);
        }

        return $imageUrl;
    }
}

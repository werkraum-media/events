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

namespace WerkraumMedia\EventsExample;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Frontend\Cache\CacheLifetimeCalculator;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

#[Autoconfigure(public: true)]
final class UserFunc
{
    private ContentObjectRenderer $cObj;

    public function __construct(
        private readonly CacheLifetimeCalculator $cacheLifetimeCalculator,
        private readonly Context $context,
    ) {
    }

    public function setContentObjectRenderer(ContentObjectRenderer $cObj): void
    {
        $this->cObj = $cObj;
    }

    #[AsAllowedCallable]
    public function accessTsfeTimeout(string $content, array $configuration, ServerRequestInterface $request): string
    {
        $pageInformation = $request->getAttribute('frontend.page.information');
        $typoScriptConfigArray = $request->getAttribute('frontend.typoscript')->getConfigArray();

        // TODO: typo3/cms-core:15 Remove the conditional block from v13.
        $numberOfArguments = (new ReflectionClass($this->cacheLifetimeCalculator::class))->getMethod('calculateLifetimeForPage')->getNumberOfParameters();
        if ($numberOfArguments === 5) {
            return 'get_cache_timeout: ' . $this->cacheLifetimeCalculator->calculateLifetimeForPage(
                $pageInformation->getId(),
                $pageInformation->getPageRecord(),
                $typoScriptConfigArray,
                0,
                $this->context
            );
        }

        return 'get_cache_timeout: ' . $this->cacheLifetimeCalculator->calculateLifetimeForPage(
            $pageInformation->getId(),
            $pageInformation->getPageRecord(),
            $typoScriptConfigArray,
            $this->context
        );
    }

    #[AsAllowedCallable]
    public function sleep(string $content, array $configuration): string
    {
        $sleep = (int)$this->cObj->stdWrapValue('sleep', $configuration['userFunc.'], 0);
        sleep($sleep);
        return 'Sleep for ' . $sleep . ' seconds';
    }
}

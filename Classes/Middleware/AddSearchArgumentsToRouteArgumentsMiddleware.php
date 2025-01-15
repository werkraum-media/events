<?php

declare(strict_types=1);

/*
 * Copyright (C) 2025 Daniel Siepmann <daniel.siepmann@codappix.com>
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

namespace WerkraumMedia\Events\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Right now I couldn't find any way to add the search parameter to the pagination via addQueryString.
 * The issue is that these are not part of TYPO3 routing. Nested lists like categories also seem impossible to configure for routing.
 *
 * See corresponding core issue: https://forge.typo3.org/issues/105941
 */
final class AddSearchArgumentsToRouteArgumentsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $routingPath = 'events/search',
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $previousResult = $request->getAttribute('routing', null);
        if ($previousResult instanceof PageArguments) {
            $request = $request->withAttribute('routing', $this->extendPageArguments($previousResult));
        }

        return $handler->handle($request);
    }

    private function extendPageArguments(PageArguments $pageArguments): PageArguments
    {
        $dynamicArguments = $pageArguments->getDynamicArguments();

        if (ArrayUtility::isValidPath($dynamicArguments, $this->routingPath) === false) {
            return $pageArguments;
        }

        $routeArguments = ArrayUtility::setValueByPath(
            $pageArguments->getRouteArguments(),
            $this->routingPath,
            ArrayUtility::getValueByPath($dynamicArguments, $this->routingPath)
        );
        $remainingArguments = ArrayUtility::removeByPath($dynamicArguments, $this->routingPath);

        return new PageArguments(
            $pageArguments->getPageId(),
            $pageArguments->getPageType(),
            $routeArguments,
            $pageArguments->getStaticArguments(),
            $remainingArguments,
        );
    }
}

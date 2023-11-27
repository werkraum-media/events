<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Controller;

/*
 * Copyright (C) 2021 Daniel Siepmann <coding@daniel-siepmann.de>
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

use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3Fluid\Fluid\View\ViewInterface;
use WerkraumMedia\Events\Caching\CacheManager;

class AbstractController extends ActionController
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Extend to add cache tag to page.
     * This allows to clear pages on modifications.
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();

        $cObject = $this->request->getAttribute('currentContentObject');
        if ($cObject instanceof ContentObjectRenderer) {
            $this->cacheManager->addAllCacheTagsToPage($cObject);
        }
    }

    /**
     * Extend original to also add data from current cobject if available.
     */
    protected function resolveView(): ViewInterface
    {
        $view = parent::resolveView();

        $view->assign('data', []);
        $cObject = $this->request->getAttribute('currentContentObject');
        if ($cObject instanceof ContentObjectRenderer && is_array($cObject->data)) {
            $view->assign('data', $cObject->data);
        }

        return $view;
    }

    protected function trigger404(string $message): void
    {
        throw new PropagateResponseException(
            GeneralUtility::makeInstance(ErrorController::class)
                ->pageNotFoundAction($this->request, $message),
            1695881170
        );
    }
}

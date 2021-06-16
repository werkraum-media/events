<?php

namespace Wrm\Events\Controller;

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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class AbstractController extends ActionController
{
    /**
     * Extend original to also add data from current cobject if available.
     */
    protected function resolveView()
    {
        $view = parent::resolveView();

        $view->assign('data', []);
        $cObject = $this->configurationManager->getContentObject();
        if ($cObject instanceof ContentObjectRenderer && is_array($cObject->data)) {
            $view->assign('data', $cObject->data);
        }

        return $view;
    }
}

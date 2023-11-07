<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;
use WerkraumMedia\Events\Controller\EventController;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    ExtensionUtility::configurePlugin(
        'Events',
        'DateSearch',
        [DateController::class => 'search'],
        [DateController::class => 'search']
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'DateList',
        [DateController::class => 'list'],
        [DateController::class => 'list']
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'DateShow',
        [DateController::class => 'show'],
        [DateController::class => 'show']
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'Selected',
        [EventController::class => 'list']
    );

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
        || is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category'] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^events_search';

    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon(
        'events-plugin',
        SvgIconProvider::class,
        ['source' => 'EXT:events/Resources/Public/Icons/Extension.svg']
    );
    $iconRegistry->registerIcon(
        'pages-module-events',
        SvgIconProvider::class,
        ['source' => 'EXT:events/Resources/Public/Icons/Folder.svg']
    );
});

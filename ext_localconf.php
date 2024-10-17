<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;
use WerkraumMedia\Events\Controller\EventController;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    ExtensionUtility::configurePlugin(
        'Events',
        'DateSearch',
        [DateController::class => 'search'],
        [DateController::class => 'search'],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'DateList',
        [DateController::class => 'list'],
        [DateController::class => 'list'],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'DateShow',
        [DateController::class => 'show'],
        [DateController::class => 'show'],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionUtility::configurePlugin(
        'Events',
        'Selected',
        [EventController::class => 'list'],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
        || is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category'] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^events_search';
});

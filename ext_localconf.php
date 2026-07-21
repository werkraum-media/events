<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;
use WerkraumMedia\Events\Controller\EventController;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    ExtensionManagementUtility::addTypoScriptSetup(
        '@import "EXT:events/Configuration/TypoScript/setup.typoscript"'
    );

    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
        || is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category'] = [];
    }

    ExtensionUtility::registerControllerActions(
        'Events',
        'DateList',
        [DateController::class => ['list']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'DateListFiltered',
        [DateController::class => ['list']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'DateListUpcoming',
        [DateController::class => ['list']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'DateListSearch',
        [DateController::class => ['search']],
        [DateController::class => ['search']],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'DateShow',
        [DateController::class => ['show']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'EventListFiltered',
        [EventController::class => ['list']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'EventListSelected',
        [EventController::class => ['list']],
        [],
    );
    ExtensionUtility::registerControllerActions(
        'Events',
        'EventShow',
        [EventController::class => ['show']],
        [],
    );

    // Search arguments are non-cacheable (POST->GET), shared `events` plugin namespace.
    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^events[search]';
});

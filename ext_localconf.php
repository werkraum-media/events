<?php

declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    if (
        isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
        || is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category']) === false
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['events_category'] = [];
    }

    $GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^events_search';
});

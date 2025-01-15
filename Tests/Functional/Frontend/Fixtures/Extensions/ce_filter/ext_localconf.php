<?php

declare(strict_types=1);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:ce_filter/Configuration/TypoScript/Setup.typoscript"'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'CeFilter',
    'filter',
    [
        \WerkraumMedia\Events\Controller\DateController::class => 'search',
    ],
    [
        \WerkraumMedia\Events\Controller\DateController::class => 'search',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

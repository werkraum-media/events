<?php

declare(strict_types=1);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:ce_list/Configuration/TypoScript/Setup.typoscript"'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'CeList',
    'list',
    [
        \WerkraumMedia\Events\Controller\DateController::class => 'list',
    ],
    [
        \WerkraumMedia\Events\Controller\DateController::class => 'list',
    ],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (string $extensionKey, string $tableName) {
    if (
        isset($GLOBALS['TCA']) === false
        ||is_array($GLOBALS['TCA']) === false
        || isset($GLOBALS['TCA'][$tableName]) === false
        ||is_array($GLOBALS['TCA'][$tableName]) === false
    ) {
        throw new \RuntimeException('TCA configuration for "' . $tableName . '" does not exist.', 1774251914);
    }

    $languagePath = 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:' . $tableName;

    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$tableName], [
        'ctrl' => [
            'typeicon_classes' => [
                'contains-events' => 'pages-module-events',
            ],
        ],
    ]);

    ExtensionManagementUtility::addTcaSelectItem(
        $tableName,
        'module',
        [
            'label' => $languagePath . '.module.events',
            'value' => 'events',
            'icon' => 'pages-module-events',
        ]
    );
})('events', 'pages');

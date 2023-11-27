<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (string $extensionKey, string $tableName) {
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

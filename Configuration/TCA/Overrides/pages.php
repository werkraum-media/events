<?php

defined('TYPO3') or die();

(static function (string $extensionKey, string $tableName) {
    $languagePath = 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:' . $tableName;

    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$tableName], [
        'ctrl' => [
            'typeicon_classes' => [
                'contains-events' => 'pages-module-events',
            ],
        ],
    ]);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        $tableName,
        'module',
        [
            0 => $languagePath . '.module.events',
            1 => 'events',
            2 => 'pages-module-events',
        ]
    );
})('events', 'pages');

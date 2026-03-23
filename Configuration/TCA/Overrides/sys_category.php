<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ArrayUtility;

(function (string $extKey, string $tableName) {
    if (
        isset($GLOBALS['TCA']) === false
        ||is_array($GLOBALS['TCA']) === false
        || isset($GLOBALS['TCA'][$tableName]) === false
        ||is_array($GLOBALS['TCA'][$tableName]) === false
    ) {
        throw new \RuntimeException('TCA configuration for "' . $tableName . '" does not exist.', 1774251914);
    }

    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$tableName], [
        'columns' => [
            'sorting' => [
                'config' => [
                    // Allow extbase to map this column to model
                    'type' => 'passthrough',
                ],
            ],
        ],
    ]);
})('events', 'sys_category');

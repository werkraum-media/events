<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ArrayUtility;

(function (string $extKey, string $table) {
    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$table], [
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

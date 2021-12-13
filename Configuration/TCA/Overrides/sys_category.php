<?php

(function (string $extKey, string $table) {
    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$table], [
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

<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ArrayUtility;

(static function (string $tableName) {
    if (
        isset($GLOBALS['TCA']) === false
        ||is_array($GLOBALS['TCA']) === false
        || isset($GLOBALS['TCA'][$tableName]) === false
        ||is_array($GLOBALS['TCA'][$tableName]) === false
    ) {
        throw new \RuntimeException('TCA configuration for "' . $tableName . '" does not exist.', 1775111009);
    }

    ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$tableName], [
        'types' => [
            'events_datelisttest' => [
                'showitem' => 'header, subheader',
            ],
            'events_dateshowtest' => [
                'showitem' => 'header, subheader',
            ],
            'events_eventshowtest' => [
                'showitem' => 'header, subheader',
            ],
        ],
    ]);
})('tt_content');

<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Resource\File;

return [
    'sys_file' => [
        [
            'uid' => 1,
            'pid' => 0,
            'missing' => 0,
            'storage' => 1,
            'type' => File::FILETYPE_IMAGE,
            'identifier' => '/staedte/beispielstadt/events/bf126089c94f95031fa07bf9d7d9b10c3e58aafebdef31f0b60604da13019b8d.jpg',
            'extension' => 'jpg',
            'name' => 'bf126089c94f95031fa07bf9d7d9b10c3e58aafebdef31f0b60604da13019b8d.jpg',
        ],
    ],
    'sys_file_reference' => [
        [
            'uid' => 1,
            'pid' => 2,
            'uid_local' => 1,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => 1,
            'title' => null,
            'description' => null,
            'alternative' => null,
        ],
    ],
    'sys_file_metadata' => [
        [
            'uid' => 1,
            'pid' => 0,
            'file' => 1,
            'title' => null,
        ],
    ],
];

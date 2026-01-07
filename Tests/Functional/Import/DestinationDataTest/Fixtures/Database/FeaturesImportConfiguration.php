<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_import' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example for test',
            'storage_pid' => '2',
            'features_pid' => '3',
            'features_parent' => '4',
            'files_folder' => '1:/staedte/beispielstadt/events/',
            'rest_experience' => 'beispielstadt',
            'rest_license_key' => 'example-license',
            'rest_mode' => 'next_months,12',
        ],
    ],
    'pages' => [
        [
            'pid' => '2',
            'uid' => '3',
            'title' => 'Features Storage',
            'doktype' => '254',
        ],
    ],
    'sys_category' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Top Category',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Event Category Parent',
            'parent' => '1',
        ],
        [
            'uid' => '4',
            'pid' => '2',
            'title' => 'Event Feature Parent',
            'parent' => '1',
        ],
        [
            'uid' => '5',
            'pid' => '3',
            'title' => 'vorhandenes Feature',
            'parent' => '4',
            'hidden' => '1',
        ],
    ],
];

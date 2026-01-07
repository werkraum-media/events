<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_import' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example import configuration',
            'storage_pid' => 2,
            'files_folder' => '1:/staedte/beispielstadt/events/',
            'region' => '1',
            'rest_experience' => 'beispielstadt',
            'rest_license_key' => 'example-license',
            'rest_mode' => 'next_months,12',
            'categories_pid' => 2,
            'category_parent' => 2,
        ],
    ],
    'sys_category' => [
        [
            'uid' => 1,
            'pid' => 2,
            'parent' => 0,
            'title' => 'Events Root',
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'parent' => 1,
            'title' => 'Events Categories',
        ],
        [
            'uid' => 3,
            'pid' => 2,
            'parent' => 1,
            'title' => 'Custom Parent',
        ],
        [
            'uid' => 4,
            'pid' => 2,
            'parent' => 3,
            'title' => 'Custom Category',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 2,
            'title' => 'Event for categories event',
            'global_id' => 'e_100350503',
            'categories' => 1,
        ],
    ],
    'sys_category_record_mm' => [
        [
            'uid_local' => 4,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting' => 0,
            'sorting_foreign' => 1,
        ],
    ],
];

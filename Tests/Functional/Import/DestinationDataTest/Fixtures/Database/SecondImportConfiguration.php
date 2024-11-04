<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_import' => [
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Second Example import configuration',
            'storage_pid' => '3',
            'files_folder' => '1:/staedte/anderestadt/events/',
            'categories_pid' => '2',
            'category_parent' => '2',
            'region' => '1',
            'rest_experience' => 'anderestadt',
            'rest_license_key' => 'example-license',
            'rest_limit' => '3',
            'rest_mode' => 'next_months,12',
            'rest_search_query' => 'name:"Beispiel2"',
        ],
    ],
    'pages' => [
        [
            'pid' => '1',
            'uid' => '3',
            'title' => 'Storage',
            'doktype' => '254',
        ],
    ],
];

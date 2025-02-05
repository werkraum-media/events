<?php

declare(strict_types=1);

return  [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '1',
            'CType' => 'events_dateshowtest',
            'header' => 'Singleview',
        ],
    ],
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Title of Event with associated page',
            'hidden' => '0',
            'pages' => '3',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => '1676419200',
            'end' => '1676484000',
        ],
    ],
    'pages' => [
        [
            'uid' => 3,
            'pid' => 1,
            'title' => 'Page 3 Referenced from event',
            'slug' => '/referenced',
        ],
    ],
];

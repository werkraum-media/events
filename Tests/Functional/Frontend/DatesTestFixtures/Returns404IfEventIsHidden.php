<?php

declare(strict_types=1);

return  [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '1',
            'CType' => 'list',
            'list_type' => 'events_dateshow',
            'header' => 'Singleview',
        ],
    ],
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event 1 starts before search, ends before search',
            'hidden' => '1',
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
];

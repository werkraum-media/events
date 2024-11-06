<?php

declare(strict_types=1);

return  [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '1',
            'CType' => 'events_datelisttest',
            'header' => 'All Dates',
        ],
    ],
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event 1 hidden',
            'hidden' => '1',
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Event 2 visible',
            'hidden' => '0',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => '1662458400',
            'end' => '1662469200',
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'event' => '2',
            'start' => '1662458400',
            'end' => '1662469200',
        ],
    ],
];

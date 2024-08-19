<?php

declare(strict_types=1);

return  [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '1',
            'CType' => 'events_datelist',
            'header' => 'All Dates',
        ],
    ],
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event 1 starts before search, ends before search',
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Event 2 starts before search, no end',
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'title' => 'Event 3 starts after search, ends after search',
        ],
        3 => [
            'uid' => '4',
            'pid' => '2',
            'title' => 'Event 4 starts after search, no end',
        ],
        4 => [
            'uid' => '5',
            'pid' => '2',
            'title' => 'Event 5 starts before search, ends after search',
        ],
        5 => [
            'uid' => '6',
            'pid' => '2',
            'title' => 'Event 6 starts inside search, ends inside search',
        ],
        6 => [
            'uid' => '7',
            'pid' => '2',
            'title' => 'Event 7 starts inside search, ends after search',
        ],
        7 => [
            'uid' => '8',
            'pid' => '2',
            'title' => 'Event 8 starts inside search, no end',
        ],
        8 => [
            'uid' => '9',
            'pid' => '2',
            'title' => 'Event 9 starts before search, ends inside search',
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
        1 => [
            'uid' => '2',
            'pid' => '2',
            'event' => '2',
            'start' => '1676419200',
            'end' => '\\NULL',
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'event' => '3',
            'start' => '1676678400',
            'end' => '1676743200',
        ],
        3 => [
            'uid' => '4',
            'pid' => '2',
            'event' => '4',
            'start' => '1676678400',
            'end' => '\\NULL',
        ],
        4 => [
            'uid' => '5',
            'pid' => '2',
            'event' => '5',
            'start' => '1676419200',
            'end' => '1676678400',
        ],
        5 => [
            'uid' => '6',
            'pid' => '2',
            'event' => '6',
            'start' => '1676559600',
            'end' => '1676570400',
        ],
        6 => [
            'uid' => '7',
            'pid' => '2',
            'event' => '7',
            'start' => '1676559600',
            'end' => '1676678400',
        ],
        7 => [
            'uid' => '8',
            'pid' => '2',
            'event' => '8',
            'start' => '1676559600',
            'end' => '\\NULL',
        ],
        8 => [
            'uid' => '9',
            'pid' => '2',
            'event' => '9',
            'start' => '1676419200',
            'end' => '1676570400',
        ],
    ],
];

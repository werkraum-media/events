<?php

declare(strict_types=1);

return [
    'tt_content' => [
        [
            'pid' => '1',
            'uid' => '1',
            'CType' => 'cefilter_filter',
            'header' => 'Search Form',
        ],
        [
            'pid' => '1',
            'uid' => '2',
            'CType' => 'celist_list',
            'header' => 'Search Results',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event one',
            'teaser' => 'Some Teaser',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Event two',
            'teaser' => 'Another teaser',
        ],
    ],
    'tx_events_domain_model_date' => [
        [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => '1661626800',
            'end' => '1661632200',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start' => '1660158000',
            'end' => '1660163400',
        ],
        [
            'uid' => '3',
            'pid' => '2',
            'event' => '2',
            'start' => '1661194800',
            'end' => '1661200200',
        ],
    ],
];

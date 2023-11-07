<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 2,
            'title' => 'Event for modifying event',
            'global_id' => 'e_100350503',
            'dates' => 2,
        ],
    ],
    'tx_events_domain_model_date' => [
        [
            'uid' => 1,
            'pid' => 2,
            'event' => 1,
            'start' => 4_097_728_800,
            'end' => null,
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'event' => 1,
            'start' => 4_097_815_200,
            'end' => null,
        ],
    ],
];

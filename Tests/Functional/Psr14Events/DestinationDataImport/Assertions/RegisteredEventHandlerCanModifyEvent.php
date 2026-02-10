<?php

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
            'start' => (new DateTimeImmutable('Saturday 2025-11-01 11:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => null,
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'event' => 1,
            'start' => (new DateTimeImmutable('Sunday 2025-11-02 11:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => null,
        ],
    ],
];

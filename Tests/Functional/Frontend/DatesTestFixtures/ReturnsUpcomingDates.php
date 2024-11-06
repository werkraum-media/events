<?php

declare(strict_types=1);

return [
    'tt_content' => [
        [
            'uid' => 1,
            'pid' => 1,
            'CType' => 'events_datelisttest',
            'header' => 'Upcoming Dates',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 2,
            'title' => 'Event 1',
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'title' => 'Event 2',
        ],
    ],
    'tx_events_domain_model_date' => [
        [
            'uid' => 1,
            'pid' => 2,
            'event' => 1,
            'start' => (new DateTimeImmutable())->modify('-5 minutes')->format('U'),
            'end' => (new DateTimeImmutable())->modify('+5 minutes')->format('U'),
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'event' => 2,
            'start' => (new DateTimeImmutable())->modify('+5 minutes')->format('U'),
            'end' => (new DateTimeImmutable())->modify('+15 minutes')->format('U'),
        ],
    ],
];

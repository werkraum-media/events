<?php

declare(strict_types=1);

return  [
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Kurzführung - Historische Altstadt',
            'global_id' => 'e_100354481',
            'dates' => '1',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Wednesday 2022-07-13 15:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Wednesday 2022-07-13 16:30:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
    ],
];

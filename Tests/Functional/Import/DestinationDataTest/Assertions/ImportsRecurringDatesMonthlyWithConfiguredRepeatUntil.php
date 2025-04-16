<?php

declare(strict_types=1);

return  [
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Tüftlerzeit',
            'global_id' => 'e_100354481',
            'dates' => '3',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Saturday 2022-08-06 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Saturday 2022-08-06 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Saturday 2022-09-03 10:00:00.', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Saturday 2022-09-03 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Saturday 2022-10-01 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Saturday 2022-10-01 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
    ],
];

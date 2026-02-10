<?php

declare(strict_types=1);

return  [
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Tüftlerzeit',
            'global_id' => 'e_100354481',
            'dates' => '4',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start'  => (new DateTimeImmutable('Saturday 2022-07-16 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end'  => (new DateTimeImmutable('Saturday 2022-07-16 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start'  => (new DateTimeImmutable('Saturday 2022-07-23 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end'  => (new DateTimeImmutable('Saturday 2022-07-23 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'event' => '1',
            'start'  => (new DateTimeImmutable('Saturday 2022-07-30 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end'  => (new DateTimeImmutable('Saturday 2022-07-30 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        3 => [
            'uid' => '4',
            'pid' => '2',
            'event' => '1',
            'start'  => (new DateTimeImmutable('Saturday 2022-08-06 10:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end'  => (new DateTimeImmutable('Saturday 2022-08-06 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
    ],
];

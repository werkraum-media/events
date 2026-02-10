<?php

declare(strict_types=1);

return  [
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Kurzführung - Historische Altstadt',
            'global_id' => 'e_100354481',
            'dates' => '10',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Wednesday 2022-07-13 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Wednesday 2022-07-13 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        1 => [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Thursday 2022-07-14 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Thursday 2022-07-14 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        2 => [
            'uid' => '3',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Friday 2022-07-15 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Friday 2022-07-15 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        3 => [
            'uid' => '4',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Saturday 2022-07-16 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Saturday 2022-07-16 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        4 => [
            'uid' => '5',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Sunday 2022-07-17 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Sunday 2022-07-17 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        5 => [
            'uid' => '6',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Monday 2022-07-18 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Monday 2022-07-18 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        6 => [
            'uid' => '7',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Tuesday 2022-07-19 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Tuesday 2022-07-19 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        7 => [
            'uid' => '8',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Wednesday 2022-07-20 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Wednesday 2022-07-20 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        8 => [
            'uid' => '9',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Thursday 2022-07-21 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Thursday 2022-07-21 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
        9 => [
            'uid' => '10',
            'pid' => '2',
            'event' => '1',
            'start' => (new DateTimeImmutable('Friday 2022-07-22 16:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end' => (new DateTimeImmutable('Friday 2022-07-22 17:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
        ],
    ],
];

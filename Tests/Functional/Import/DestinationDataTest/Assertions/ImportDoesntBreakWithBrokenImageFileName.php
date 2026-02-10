<?php

declare(strict_types=1);

return  [
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'deleted' => '0',
            'hidden' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'sys_language_uid' => '-1',
            'l10n_parent' => '0',
            't3ver_oid' => '0',
            't3ver_wsid' => '0',
            't3ver_state' => '0',
            't3ver_stage' => '0',
            'title' => 'Allerlei Weihnachtliches (Heute mit Johannes Geißer)',
            'subtitle' => '',
            'global_id' => 'e_100347853',
            'highlight' => '0',
            'teaser' => '',
            'dates' => '1',
        ],
    ],
    'tx_events_domain_model_date' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'hidden' => '0',
            'starttime' => '0',
            'endtime' => '0',
            'sys_language_uid' => '-1',
            'l10n_parent' => '0',
            't3ver_oid' => '0',
            't3ver_wsid' => '0',
            't3ver_state' => '0',
            'event' => '1',
            'start'  => (new DateTimeImmutable('Saturday 2099-12-19 15:00:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'end'  => (new DateTimeImmutable('Saturday 2099-12-19 16:30:00', new DateTimeZone('Europe/Berlin')))->getTimestamp(),
            'canceled' => 'no',
            'postponed_date' => '0',
            'canceled_link' => '',
        ],
    ],
    'sys_file' => [
    ],
    'sys_file_reference' => [
    ],
];

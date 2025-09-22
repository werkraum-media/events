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
            'start' => '4101372000',
            'end' => '4101377400',
            'canceled' => 'no',
            'postponed_date' => '0',
            'canceled_link' => '',
        ],
    ],
    'sys_file' => [
        [
            'uid' => '1',
            'pid' => '0',
            'identifier' => '/staedte/beispielstadt/events/e908cfc8c35e616b5d8fa02e099bb2b597aa1cb64edd5dad62778d3c460ea1c3.png',
            'extension' => 'png',
            'mime_type' => 'image/png',
        ],
    ],
    'sys_file_reference' => [
        [
            'uid' => 1,
            'pid' => 2,
            'deleted' => 0,
            'hidden' => 0,
            'sys_language_uid' => 0,
            'uid_local' => 1,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => 1,
        ],
    ],
];

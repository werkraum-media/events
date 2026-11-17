<?php

declare(strict_types=1);

return [
    'tt_content' => [
        [
            'pid' => '1',
            'uid' => '1',
            'CType' => 'events_eventlisttest',
            'header' => 'Events',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event from Weimar',
            'origin_configuration_uid' => '33',
            'origin_configuration_title' => 'Weimar Sync Channel',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Event from Erfurt',
            'origin_configuration_uid' => '32',
            'origin_configuration_title' => 'Erfurt Sync Channel',
        ],
        [
            'uid' => '3',
            'pid' => '2',
            'title' => 'Event without origin',
        ],
    ],
];

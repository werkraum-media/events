<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_event' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Event from Weimar',
            'origin_configuration_title' => 'Weimar Sync Channel',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Another event from Weimar',
            'origin_configuration_title' => 'Weimar Sync Channel',
        ],
        [
            'uid' => '3',
            'pid' => '2',
            'title' => 'Event from Erfurt',
            'origin_configuration_title' => 'Erfurt Sync Channel',
        ],
        [
            'uid' => '4',
            'pid' => '2',
            'title' => 'Event without origin',
        ],
        [
            'uid' => '5',
            'pid' => '2',
            'title' => 'Deleted event from Gotha',
            'origin_configuration_title' => 'Gotha Sync Channel',
            'deleted' => '1',
        ],
    ],
];

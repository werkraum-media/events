<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_location' => [
        [
            'uid' => 1,
            'pid' => 0,
            'sys_language_uid' => -1,
            'global_id' => 'a91656ec76732f2b7b72987d11d81d926fa67ea3b2eb4cc6fd75bb2b748da21d',
            'name' => 'Domplatz',
            'street' => '',
            'city' => 'Erfurt',
            'zip' => '99084',
            'district' => 'Altstadt',
            'country' => 'Deutschland',
            'phone' => '',
            'latitude' => '50.977089',
            'longitude' => '11.024878',
        ],
        [
            'uid' => 3,
            'pid' => 0,
            'sys_language_uid' => -1,
            'global_id' => '95ca076b77e478cc8eb831f48aacaa608a640034e31da2e11b42da9758c84aaf',
            'name' => 'Wenigemarkt',
            'street' => '',
            'city' => 'Erfurt',
            'zip' => '99084',
            'district' => 'Altstadt',
            'country' => 'Deutschland',
            'phone' => '',
            'latitude' => '50.978500',
            'longitude' => '11.031589',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 0,
            'title' => 'Abendmahlsgottesdienst',
            'global_id' => 'e_100171396',
            'location' => 1,
        ],
        [
            'uid' => 2,
            'pid' => 0,
            'title' => 'Travestie-Revue "Pretty Wo(man)"',
            'global_id' => 'e_100172162',
            'location' => 1,
        ],
        [
            'uid' => 3,
            'pid' => 0,
            'title' => 'Abendgebet in englischer Sprache',
            'global_id' => 'e_100172275',
            'location' => 3,
        ],
    ],
];

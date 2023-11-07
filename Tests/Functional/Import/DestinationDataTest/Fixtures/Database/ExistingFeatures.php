<?php

declare(strict_types=1);

return [
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 2,
            'global_id' => 'e_100347853',
            'features' => 1,
        ],
    ],
    'sys_category_record_mm' => [
        [
            'uid_local' => 5,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'features',
            'sorting' => 0,
            'sorting_foreign' => 1,
        ],
    ],
];

<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Domain\Repository\PageRepository;

// Three events in a storage folder (pid 11), each hitting the free-text search
// through a DIFFERENT field, plus two sys_categories to exercise category filtering.
//
// Museum (10) → Goethehaus; Kirche (11) → Domberg. Stadtmuseum has no category.
return [
    'pages' => [
        [
            'uid' => 1,
            'pid' => 0,
            'title' => 'Root',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
            'slug' => '/',
            'deleted' => 0,
        ],
        [
            'uid' => 11,
            'pid' => 1,
            'title' => 'Storage for Events',
            'doktype' => PageRepository::DOKTYPE_SYSFOLDER,
            'deleted' => 0,
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 11,
            'title' => 'Stadtmuseum Erfurt',
            'subtitle' => '',
            'teaser' => '',
            'details' => '',
            'categories' => 0,
        ],
        [
            'uid' => 2,
            'pid' => 11,
            'title' => 'Domberg Erfurt',
            'subtitle' => 'Wahrzeichen der Stadt',
            'teaser' => '',
            'details' => '',
            'categories' => 1,
        ],
        [
            'uid' => 3,
            'pid' => 11,
            'title' => 'Goethehaus Weimar',
            'subtitle' => '',
            'teaser' => 'Wohnhaus des Dichters',
            'details' => 'Ausführliche Beschreibung mit Schlagwort Klassik.',
            'categories' => 1,
        ],
    ],
    'sys_category' => [
        [
            'uid' => 10,
            'pid' => 11,
            'title' => 'Museum',
        ],
        [
            'uid' => 11,
            'pid' => 11,
            'title' => 'Kirche',
        ],
    ],
    'sys_category_record_mm' => [
        // Goethehaus (3) → Museum (10)
        [
            'uid_local' => 10,
            'uid_foreign' => 3,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting_foreign' => 1,
        ],
        // Domberg (2) → Kirche (11)
        [
            'uid_local' => 11,
            'uid_foreign' => 2,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting_foreign' => 1,
        ],
    ],
];

<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Resource\File;

return [
    'sys_file' => [
        [
            'uid' => 1,
            'pid' => 0,
            'missing' => 0,
            'storage' => 1,
            'type' => File::FILETYPE_IMAGE,
            'identifier' => '/staedte/beispielstadt/events/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
            'extension' => 'jpg',
            'name' => 'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
        ],
        [
            'uid' => 2,
            'pid' => 0,
            'missing' => 0,
            'storage' => 1,
            'type' => File::FILETYPE_IMAGE,
            'identifier' => '/staedte/beispielstadt/events/tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            'extension' => 'jpg',
            'name' => 'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
        ],
    ],
    'sys_file_reference' => [
        [
            'uid' => 1,
            'pid' => 2,
            'uid_local' => 1,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => 1,
            'title' => null,
            'description' => null,
            'alternative' => null,
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'uid_local' => 2,
            'uid_foreign' => 1,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => 2,
            'title' => null,
            'description' => null,
            'alternative' => null,
        ],
    ],
    'sys_file_metadata' => [
        [
            'uid' => 1,
            'pid' => 0,
            'file' => 1,
            'title' => 'Theater-Rudolstadt_Johannes-Geißer_photo-by-Lisa-Stern_web_.jpg',
            'description' => '',
            'alternative' => '',
            'creator_tool' => 'destination.one',
            'source' => 'https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
            'copyright' => 'Max Mustermann',
        ],
        [
            'uid' => 2,
            'pid' => 0,
            'file' => 2,
            'title' => 'Tueftlerzeit©SFZ-Rudolstadt.jpg',
            'description' => 'Description of Tueftlerzeit',
            'alternative' => 'Description of Tueftlerzeit',
            'creator_tool' => 'destination.one',
            'source' => 'https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            'copyright' => '',
        ],
    ],
];

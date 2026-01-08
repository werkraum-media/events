<?php

declare(strict_types=1);
use TYPO3\CMS\Core\Resource\File;

return [
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 2,
            'global_id' => 'e_100347853',
            'import_configuration' => 1,
            'title' => 'Allerlei Weihnachtliches (Heute mit Johannes Geißer)',
        ],
        [
            'uid' => 2,
            'pid' => 2,
            'global_id' => 'e_100354483',
            'import_configuration' => 1,
            'title' => 'Will be removed',
        ],
        [
            'uid' => 3,
            'pid' => 2,
            'global_id' => 'e_100350503',
            'import_configuration' => 2,
            'title' => 'Adventliche Orgelmusik (Orgel: KMD Frank Bettenhausen)',
        ],
    ],
    'sys_file' => [
        [
            'uid' => 1,
            'pid' => 0,
            'missing' => 0,
            'storage' => 1,
            'type' => File::FILETYPE_IMAGE,
            'identifier' => '/staedte/beispielstadt/events/for-removal.jpg',
            'extension' => 'jpg',
            'name' => 'for-removal.jpg',
            'identifier_hash' => 'fe4fcc840baa706899f7060096f693a01d8be36d',
            'folder_hash' => 'dcb2fdd85835a5ae315fdd7ef5cb2b859d9ec437',
            'sha1' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
        ],
    ],
    'sys_file_reference' => [
        [
            'uid' => 1,
            'pid' => 2,
            'uid_local' => 1,
            'uid_foreign' => 2,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => 1,
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
            'title' => 'for-removal.jpg',
            'description' => '',
            'alternative' => '',
            'copyright' => 'John Doe',
        ],
    ],
];

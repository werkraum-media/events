<?php

$EM_CONF['events'] = [
    'title' => 'Events',
    'description' => 'Extension to manage events',
    'category' => 'plugin',
    'author' => 'Dirk Koritnik',
    'author_email' => 'koritnik@werkraum-media.de',
    'state' => 'alpha',
    'uploadfolder' => 1,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

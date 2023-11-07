<?php

declare(strict_types=1);

$EM_CONF['example'] = [
    'title' => 'Events Test',
    'category' => 'plugin',
    'description' => 'Example for tests',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'events' => '',
            'typo3' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

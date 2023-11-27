<?php

declare(strict_types=1);

$EM_CONF['custom_event'] = [
    'title' => 'Custom Events',
    'description' => 'Integrates custom event specifics',
    'category' => 'plugin',
    'author' => 'Daniel Siepmann',
    'author_email' => 'coding@daniel-siepmann.de',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'events' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

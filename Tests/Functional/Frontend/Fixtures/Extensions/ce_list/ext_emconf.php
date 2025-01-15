<?php

declare(strict_types=1);

$EM_CONF['ce_list'] = [
    'title' => 'Events list view',
    'description' => 'Content Elements for \'Events\'',
    'category' => 'plugin',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '*',
            'events' => '*',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];

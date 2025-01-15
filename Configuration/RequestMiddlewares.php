<?php

declare(strict_types=1);

use WerkraumMedia\Events\Middleware\AddSearchArgumentsToRouteArgumentsMiddleware;

return [
    'frontend' => [
        'werkraummedia/events/add-search-arguments-to-route-arguments' => [
            'target' => AddSearchArgumentsToRouteArgumentsMiddleware::class,
            'after' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'before' => [
                'typo3/cms-frontend/page-argument-validator',
            ],
        ],
    ],
];

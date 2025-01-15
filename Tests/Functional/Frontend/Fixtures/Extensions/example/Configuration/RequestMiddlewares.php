<?php

declare(strict_types=1);

use WerkraumMedia\EventsExample\TestingDateTimeAspectMiddleware;

return [
    'frontend' => [
        'werkraummedia/events/testing-date-time-aspect' => [
            'target' => TestingDateTimeAspectMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
];

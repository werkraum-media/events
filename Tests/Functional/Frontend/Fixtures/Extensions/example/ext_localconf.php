<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;
use WerkraumMedia\Events\Controller\EventController;

ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [DateController::class => 'list']
);

ExtensionUtility::configurePlugin(
    'Events',
    'EventShow',
    [EventController::class => 'show']
);

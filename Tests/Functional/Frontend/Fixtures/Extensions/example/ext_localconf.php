<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;
use WerkraumMedia\Events\Controller\EventController;

ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [DateController::class => 'list'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Events',
    'DateShowTest',
    [DateController::class => 'show'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Events',
    'EventShowTest',
    [EventController::class => 'show'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

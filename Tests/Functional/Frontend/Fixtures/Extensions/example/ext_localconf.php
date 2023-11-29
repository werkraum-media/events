<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [\Wrm\Events\Controller\DateController::class => 'list']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Events',
    'EventShow',
    [\Wrm\Events\Controller\EventController::class => 'show']
);

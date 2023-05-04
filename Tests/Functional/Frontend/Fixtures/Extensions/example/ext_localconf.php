<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [\Wrm\Events\Controller\DateController::class => 'list']
);

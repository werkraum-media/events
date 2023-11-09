<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [\WerkraumMedia\Events\Controller\DateController::class => 'list']
);

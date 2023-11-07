<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Events\Controller\DateController;

ExtensionUtility::configurePlugin(
    'Events',
    'DateListTest',
    [DateController::class => 'list']
);

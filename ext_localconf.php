<?php

defined('TYPO3') || die('Access denied.');

call_user_func(
    function () {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Events',
            'DateSearch',
            [\Wrm\Events\Controller\DateController::class => 'search'],
            [\Wrm\Events\Controller\DateController::class => 'search']
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Events',
            'DateList',
            [\Wrm\Events\Controller\DateController::class => 'list'],
            [\Wrm\Events\Controller\DateController::class => 'list']
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Events',
            'DateShow',
            [\Wrm\Events\Controller\DateController::class => 'show'],
            [\Wrm\Events\Controller\DateController::class => 'show']
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Events',
            'Selected',
            [\Wrm\Events\Controller\EventController::class => 'list']
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'events-plugin',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:events/Resources/Public/Icons/Extension.svg']
        );
    }

);

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
            ['source' => 'EXT:events/Resources/Public/Icons/user_plugin_events.svg']
        );

        /*
        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        events {
                            iconIdentifier = events-plugin
                            title = LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:tx_events.name
                            description = LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:tx_events.description
                            tt_content_defValues {
                                CType = list
                                list_type = events_pi1
                            }
                        }
                    }
                    show = *
                }
            }'
        );
        */
    }
);

<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Wrm.Events',
            'Pi1',
            [
                'Event' => 'teaser, list, show, search',
                'Date' => 'teaser, list, show, search'
            ],
            [
                'Event' => 'teaser, list, show, search',
                'Date' => 'teaser, list, show, search'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Wrm.Events',
            'DateSearch',
            [
                'Date' => 'search'
            ],
            [
                'Date' => 'search'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Wrm.Events',
            'DateList',
            [
                'Date' => 'list'
            ],
            [
                'Date' => 'list'
            ]
        );

        /*
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

        $iconRegistry->registerIcon(
            'events-plugin',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:events/Resources/Public/Icons/user_plugin_events.svg']
        );

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

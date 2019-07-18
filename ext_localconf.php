<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Wrm.Events',
            'Pi1',
            [
                'Event' => 'teaser, list, show',
                'Date' => 'teaser, list, show'
            ],
            [
                'Event' => 'teaser, list, show',
                'Date' => 'teaser, list, show'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    events {
                        iconIdentifier = events-plugin-events
                        title = LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:txevents_events.name
                        description = LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:tx_events_events.description
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
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
        $iconRegistry->registerIcon(
            'events-plugin-events',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:events/Resources/Public/Icons/user_plugin_events.svg']
        );
		
    }
);

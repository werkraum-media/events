<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {

        /*
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Wrm.Events',
            'Pi1',
            'Events',
            'EXT:events/Resources/Public/Icons/user_plugin_events.svg'
        );
        */

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('events', 'Configuration/TypoScript', 'Events');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_events_domain_model_event', 'EXT:events/Resources/Private/Language/locallang_csh_tx_events_domain_model_event.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ddevents_domain_model_event');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_events_domain_model_organizer', 'EXT:events/Resources/Private/Language/locallang_csh_tx_events_domain_model_organizer.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_events_domain_model_organizer');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_events_domain_model_date', 'EXT:events/Resources/Private/Language/locallang_csh_tx_events_domain_model_date.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_events_domain_model_date');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_events_domain_model_region', 'EXT:events/Resources/Private/Language/locallang_csh_tx_events_domain_model_region.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_events_domain_model_region');

    }
);

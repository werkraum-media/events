<?php

defined('TYPO3_MODE') or die();

call_user_func(function () {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Wrm.Events',
        'Pi1',
        'Events Plugin',
        'EXT:events/Resources/Public/Icons/user_plugin_events.svg'
    );

    $pluginSignature = 'events_pi1';

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:events/Configuration/FlexForms/Pi1.xml'
    );


});

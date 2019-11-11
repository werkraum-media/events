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

    /* Search Plugin */

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Wrm.Events',
        'DateSearch',
        'Events Date Search',
        'EXT:events/Resources/Public/Icons/user_plugin_events.svg'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['events_datesearch'] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_datesearch',
        'FILE:EXT:events/Configuration/FlexForms/Search.xml'
    );

    /* Date List Plugin */

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Wrm.Events',
        'DateList',
        'Events Date List',
        'EXT:events/Resources/Public/Icons/user_plugin_events.svg'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['events_datelist'] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_datelist',
        'FILE:EXT:events/Configuration/FlexForms/DateList.xml'
    );


});

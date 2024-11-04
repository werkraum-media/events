<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(function (string $extKey, string $table) {
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'][$extKey] = 'Events';

    /* Search Plugin */
    $pluginSignature = ExtensionUtility::registerPlugin(
        'Events',
        'DateSearch',
        'Events: Date Search',
        'events-plugin',
        $extKey
    );
    ExtensionManagementUtility::addToAllTCAtypes($table, 'pi_flexform', $pluginSignature, 'after:subheader');
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:events/Configuration/FlexForms/DateSearch.xml',
        $pluginSignature,
    );

    /* Date List Plugin */
    $pluginSignature = ExtensionUtility::registerPlugin(
        'Events',
        'DateList',
        'Events: Date List',
        'events-plugin',
        $extKey
    );
    ExtensionManagementUtility::addToAllTCAtypes($table, 'pi_flexform', $pluginSignature, 'after:subheader');
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:events/Configuration/FlexForms/DateList.xml',
        $pluginSignature,
    );

    /* Date Show Plugin */
    $pluginSignature = ExtensionUtility::registerPlugin(
        'Events',
        'DateShow',
        'Events: Date Show',
        'events-plugin',
        $extKey
    );
    ExtensionManagementUtility::addToAllTCAtypes($table, 'pi_flexform', $pluginSignature, 'after:subheader');
    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:events/Configuration/FlexForms/DateShow.xml',
        'events_dateshow',
    );

    /* Event Selected Plugin */
    $pluginSignature = ExtensionUtility::registerPlugin(
        'Events',
        'Selected',
        'Events: Show selected',
        'events-plugin',
        $extKey
    );
    ExtensionManagementUtility::addToAllTCAtypes($table, 'pi_flexform', $pluginSignature, 'after:subheader');
    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:events/Configuration/FlexForms/Selected.xml',
        'events_selected',
    );
})('events', 'tt_content');

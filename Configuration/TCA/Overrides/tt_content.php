<?php

(function (string $extKey, string $table) {
    /* Search Plugin */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Events',
        'DateSearch',
        'Events: Date Search',
        'EXT:events/Resources/Public/Icons/Extension.svg'
    );
    $GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist']['events_datesearch'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_datesearch',
        'FILE:EXT:events/Configuration/FlexForms/DateSearch.xml'
    );

    /* Date List Plugin */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Events',
        'DateList',
        'Events: Date List',
        'EXT:events/Resources/Public/Icons/Extension.svg'
    );
    $GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist']['events_datelist'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_datelist',
        'FILE:EXT:events/Configuration/FlexForms/DateList.xml'
    );

    /* Date Show Plugin */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Events',
        'DateShow',
        'Events: Date Show',
        'EXT:events/Resources/Public/Icons/Extension.svg'
    );
    $GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist']['events_dateshow'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_dateshow',
        'FILE:EXT:events/Configuration/FlexForms/DateShow.xml'
    );

    /* Event Selected Plugin */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Events',
        'Selected',
        'Events: Show selected',
        'EXT:events/Resources/Public/Icons/Extension.svg'
    );
    $GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist']['events_selected'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'events_selected',
        'FILE:EXT:events/Configuration/FlexForms/Selected.xml'
    );
})('events', 'tt_content');

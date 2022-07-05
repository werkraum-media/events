<?php

(function (string $extKey, string $table) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
        $extKey,
        $table,
        'categories',
        [
            'label' => 'Categories',
            'fieldConfiguration' => [
                'minitems' => 0,
                'multiple' => true,
            ]
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
        $extKey,
        $table,
        'features',
        [
            'label' => 'Features',
            'fieldConfiguration' => [
                'minitems' => 0,
                'multiple' => true,
            ]
        ]
    );
})('events', 'tx_events_domain_model_event');

<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'dd_events',
    'tx_events_domain_model_event',
    'categories',
    [
        'label' => 'Categories',
        'fieldConfiguration' => [
            'minitems' => 0,
            'maxitems' => 3,
            'multiple' => true,
        ]
    ]
);
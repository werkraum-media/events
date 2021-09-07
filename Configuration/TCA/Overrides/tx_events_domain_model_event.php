<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'events',
    'tx_events_domain_model_event',
    'categories',
    [
        'label' => 'Categories',
        'fieldConfiguration' => [
            'minitems' => 0,
            'multiple' => true,
        ]
    ]
);

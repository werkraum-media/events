<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import',
        'label' => 'title',
        'label_alt' => 'rest_experience',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_import.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, hidden, --div--;LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.div.typo3, --palette--;;typo3_storage, --palette--;;categories, --palette--;;features,--palette--;;relations, --div--;LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.div.rest, rest_experience, rest_search_query',
        ],
    ],
    'palettes' => [
        'typo3_storage' => [
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.palette.typo3_storage',
            'showitem' => 'storage_pid, files_folder',
        ],
        'categories' => [
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.palette.categories',
            'showitem' => 'category_parent, categories_pid',
        ],
        'features' => [
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.palette.features',
            'showitem' => 'features_parent, features_pid, ',
        ],
        'relations' => [
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.palette.relations',
            'showitem' => 'region',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.title',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.title.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'storage_pid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.storage_pid',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.storage_pid.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
            ],
        ],
        'region' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.region',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_events_domain_model_region',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'categories_pid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.categories_pid',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.categories_pid.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'category_parent' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.category_parent',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.category_parent.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'sys_category',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'features_pid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.features_pid',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.features_pid.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'features_parent' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.features_parent',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.features_parent.description',
            'config' => [
                'type' => 'group',
                'allowed' => 'sys_category',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'files_folder' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.files_folder',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.files_folder.description',
            'config' => [
                'type' => 'folder',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
            ],
        ],

        'rest_experience' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_experience',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_experience.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'rest_search_query' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_search_query',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_search_query.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
    ],
];

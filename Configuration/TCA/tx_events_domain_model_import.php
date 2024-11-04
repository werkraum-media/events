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
            'showitem' => implode(',', [
                'title',
                'hidden',
                '--div--;LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.div.typo3',
                '--palette--;;typo3_storage',
                '--palette--;;categories',
                '--palette--;;features',
                '--palette--;;relations',
                '--div--;LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.div.rest',
                'rest_license_key',
                'rest_experience',
                'rest_mode',
                'rest_limit',
                'rest_search_query',
                '--div--;LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.div.import',
                'import_repeat_until',
                'import_features',
            ]),
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

        'rest_license_key' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_license_key',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_license_key.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],
        'rest_experience' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_experience',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_experience.description',
            'config' => [
                'type' => 'input',
                'required' => true,
                'size' => 50,
                'max' => 255,
            ],
        ],
        'rest_mode' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_mode',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_mode.description',
            'config' => [
                'type' => 'input',
                'default' => 'next_months,12',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'rest_limit' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_limit',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_limit.description',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
                'default' => '500',
                'size' => 50,
                'required' => true,
                'range' => [
                    'lower' => 0,
                    'upper' => 5000,
                ],
            ],
        ],
        'rest_search_query' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_search_query',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.rest_search_query.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
            ],
        ],

        'import_repeat_until' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.import_repeat_until',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.import_repeat_until.description',
            'config' => [
                'type' => 'input',
                'default' => '+60 days',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'import_features' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.import_features',
            'description' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.import_features.description',
            'config' => [
                'type' => 'check',
                'cols' => 'inline',
                'default' => '0',
                'items' => [
                    [
                        'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_import.xlf:tx_events_domain_model_import.import_features.html_for_detail',
                    ],
                ],
            ],
        ],
    ],
];

<?php

$l10nPathGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
$l10nPath = 'LLL:EXT:events/Resources/Private/Language/locallang_csh_location.xlf';

return [
    'ctrl' => [
        'title' => $l10nPath . ':tx_events_domain_model_location',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name',
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_location.svg'
    ],
    'types' => [
        '1' => [
            'showitem' => '--palette--;' . $l10nPath . ':palette.general;general,
                    sys_language_uid,
                    l10n_parent,
                    l10n_diffsource,
                    hidden,
                    name,
                    slug,
                    global_id,

                    street,
                    district,
                    city,
                    zip,
                    country,
                    phone,
                    latitude,
                    longitude,
                --div--;' . $l10nPath . ':tabs.access,
                    starttime,
                    endtime'
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $l10nPathGeneral . ':LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        $l10nPathGeneral . ':LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => $l10nPathGeneral . ':LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_events_domain_model_location',
                'foreign_table_where' => 'AND {#tx_events_domain_model_location}.{#pid}=###CURRENT_PID### AND {#tx_events_domain_model_location}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => $l10nPathGeneral . ':LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => $l10nPathGeneral . ':LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $l10nPathGeneral . ':LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $l10nPathGeneral . ':LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'global_id' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.global_id',
            'description' => $l10nPath . ':tx_events_domain_model_location.global_id.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['name'],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => false,
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'district' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.district',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'country' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.country',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'latitude' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.latitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'longitude' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_location.longitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
    ],
];

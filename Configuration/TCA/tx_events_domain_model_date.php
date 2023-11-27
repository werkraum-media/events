<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date',
        'label' => 'start',
        'label_alt' => 'end, canceled',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        //'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_date.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, start, end, canceled, postponed_date, canceled_link, slug, event, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language'],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_events_domain_model_date',
                'foreign_table_where' => 'AND {#tx_events_domain_model_date}.{#pid}=###CURRENT_PID### AND {#tx_events_domain_model_date}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
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
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'start' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.start',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'size' => 12,
                'default' => null,
            ],
        ],
        'end' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.end',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'size' => 12,
                'default' => null,
            ],
        ],
        'canceled' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.canceled',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 'no',
                'items' => [
                    '0' => [
                        'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.canceled.options.no',
                        'value' => 'no',
                    ],
                    '1' => [
                        'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.canceled.options.canceled',
                        'value' => 'canceled',
                    ],
                    '2' => [
                        'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.canceled.options.postponed',
                        'value' => 'postponed',
                    ],
                ],
            ],
        ],
        'postponed_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.postponed_date',
            'displayCond' => 'FIELD:canceled:=:postponed',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_events_domain_model_date',
                'foreign_table_where' => ' AND {#tx_events_domain_model_date}.{#event} = ###REC_FIELD_event### AND {#tx_events_domain_model_date}.{#uid} != ###THIS_UID###',
                'default' => '0',
                'items' => [
                    '0' => [
                        'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.postponed_date.0',
                        'value' => '0',
                    ],
                ],
            ],
        ],
        'canceled_link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.canceled_link',
            'displayCond' => 'FIELD:canceled:=:canceled',
            'config' => [
                'type' => 'link',
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['event-title', 'start-with-time'],
                    'fieldSeparator' => '-',
                    'prefixParentPageSlug' => false,
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'event' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.event',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_events_domain_model_event',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => 1,
            ],
        ],
    ],
];

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
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_date.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, start, end, canceled, postponed_date, canceled_link, slug, event, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'start' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_date.xlf:tx_events_domain_model_date.start',
            'config' => [
                'type' => 'datetime',
                'format' => 'datetime',
                'size' => 12,
                'default' => null,
                'nullable' => true,
                'searchable' => false,
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
                'nullable' => true,
                'searchable' => false,
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
                'searchable' => false,
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
                'searchable' => false,
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

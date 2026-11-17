<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer',
        'label' => 'name',
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
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_organizer.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, street, district, city, zip, phone, web, email, --palette--;;origin, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        'origin' => [
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:origin.palette',
            'showitem' => 'origin_configuration_uid, origin_configuration_title, --linebreak--, origin_sync_id',
        ],
    ],
    'columns' => [
        'origin_configuration_uid' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:origin.origin_configuration_uid',
            'config' => [
                'type' => 'number',
                'readOnly' => true,
            ],
        ],
        'origin_configuration_title' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:origin.origin_configuration_title',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'searchable' => false,
            ],
        ],
        'origin_sync_id' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_db.xlf:origin.origin_sync_id',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
                'searchable' => false,
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'district' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.district',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'web' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.web',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:events/Resources/Private/Language/locallang_csh_organizer.xlf:tx_events_domain_model_organizer.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],

    ],
];

<?php

$l10nPathGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
$l10nPathLang = 'LLL:EXT:lang/locallang_core.xlf';
$l10nPathFE = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf';
$l10nPath = 'LLL:EXT:events/Resources/Private/Language/locallang_csh_event.xlf';

return [
    'ctrl' => [
        'title' => $l10nPath . ':tx_events_domain_model_event',
        'label' => 'title',
        'thumbnail' => 'images',
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
        'searchFields' => 'title,subtitle,global_id,teaser',
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_event.svg'
    ],
    'types' => [
        '1' => [
            'showitem' => '--palette--;' . $l10nPathFE . ':palette.general;general,
                    sys_language_uid,
                    l10n_parent,
                    l10n_diffsource,
                    hidden,
                    highlight,
                    title,
                    subtitle,
                    teaser,
                    slug,
                    ticket,
                    global_id,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.text,
                    details,
                    price_info,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.dates,
                    dates,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.location,
                    name,
                    street,
                    district,
                    city,
                    zip,
                    country,
                    phone,
                    web,
                    latitude,
                    longitude,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.relations,
                    organizer,
                    region,
                    partner,
                    categories,
                    references_events,
                    pages,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.media,
                    images,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.social,
                    facebook,
                    youtube,
                    instagram,
                --div--;' . $l10nPathFE . ':tabs.access,
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
                'foreign_table' => 'tx_events_domain_model_event',
                'foreign_table_where' => 'AND {#tx_events_domain_model_event}.{#pid}=###CURRENT_PID### AND {#tx_events_domain_model_event}.{#sys_language_uid} IN (-1,0)',
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

        'title' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.title',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2,
                'eval' => 'trim'
            ]
        ],
        'subtitle' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.subtitle',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2,
                'eval' => 'trim'
            ]
        ],
        'global_id' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.global_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => false,
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'highlight' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.highlight',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $l10nPathLang . ':labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
        ],
        'teaser' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'eval' => 'trim'
            ]
        ],
        'details' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.details',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'Default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],

        ],
        'price_info' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.price_info',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'name' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'district' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.district',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'country' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.country',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'web' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.web',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'ticket' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.ticket',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'eval' => 'trim',
                'max' => 1024,
                'size' => 50,
                'softref' => 'typolink',
            ],
        ],
        'facebook' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.facebook',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'youtube' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.youtube',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'instagram' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.instagram',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'latitude' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.latitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'longitude' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.longitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'images' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.images',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'images',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => $l10nPathFE . ':images.addFileReference',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => true,
                        'showSynchronizationLink' => true,
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'images',
                        'tablenames' => 'tx_events_domain_model_event',
                        'table_local' => 'sys_file',
                    ],
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ]
                    ],
                    'maxitems' => 8
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],

        'pages' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.pages',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
            ],
        ],

        'categories' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.categories',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],

        'dates' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.dates',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_events_domain_model_date',
                'foreign_field' => 'event',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'useSortable' => 0,
                    'expandSingle' => 1,
                    'enabledControls' => array(
                        'info' => false,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => true,
                        'hide' => false,
                        'delete' => true,
                        'localize' => false,
                    ),
                    'levelLinksPosition' => 'top',
                    'showPossibleLocalizationRecords' => false,
                    'showRemovedLocalizationRecords' => false,
                    'showSynchronizationLink' => false,
                    'showAllLocalizationLink' => false,
                ],

            ],

        ],

        'organizer' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.organizer',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_events_domain_model_organizer',
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'region' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.region',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_events_domain_model_region',
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'references_events' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.references_events',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_events_domain_model_event',
                'suggestOptions' => [
                    'tx_events_domain_model_event' => [
                        'searchCondition' => 'tx_events_domain_model_event.sys_language_uid IN (0, -1)',
                    ],
                ],
            ],
        ],

        'partner' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.partner',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_events_domain_model_partner',
                'fieldControl' => [
                    'addRecord' => [
                        'disabled' => false,
                        'pid' => '###CURRENT_PID###',
                        'table' => 'tx_events_domain_model_partner',
                    ],
                ],
            ],
        ],
    ],
];

<?php

$l10nPathGeneral = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf';
$l10nPathLang = 'LLL:EXT:lang/locallang_core.xlf';
$l10nPathFE = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf';
$l10nPath = 'LLL:EXT:events/Resources/Private/Language/locallang_csh_event.xlf';
$l10nLocationPath = 'LLL:EXT:events/Resources/Private/Language/locallang_csh_location.xlf';

return [
    'ctrl' => [
        'title' => $l10nPath . ':tx_events_domain_model_event',
        'label' => 'title',
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
        'iconfile' => 'EXT:events/Resources/Public/Icons/tx_events_domain_model_event.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => '
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
                    --palette--;;source,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.text,
                    details,
                    price_info,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.dates,
                    dates,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.location,
                    location,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.relations,
                    organizer,
                    region,
                    partner,
                    categories,
                    features,
                    keywords,
                    references_events,
                    pages,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.media,
                    images,
                --div--;' . $l10nPath . ':tx_events_domain_model_event.tabs.social,
                    web,
                    facebook,
                    youtube,
                    instagram,
                --div--;' . $l10nPathFE . ':tabs.access,
                    starttime,
                    endtime',
        ],
    ],
    'palettes' => [
        'source' => [
            'label' => $l10nPath . ':tx_events_domain_model_event.palette.source',
            'showitem' => 'source_name, source_url',
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
                        'flags-multiple',
                    ],
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
                        'invertStateDisplay' => true,
                    ],
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
                    'allowLanguageSynchronization' => true,
                ],
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
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.title',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2,
                'eval' => 'trim',
            ],
        ],
        'subtitle' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.subtitle',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 2,
                'eval' => 'trim',
            ],
        ],
        'global_id' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.global_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'source_name' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.source_name',
            'config' => [
                'type' => 'input',
                'readOnly' => true,
            ],
        ],
        'source_url' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.source_url',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'softref' => 'typolink',
                'readOnly' => true,
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
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => false,
                    ],
                ],
                'default' => 0,
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],
        'details' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.details',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
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
                'eval' => 'trim',
            ],
        ],
        'web' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.web',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
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
                'eval' => 'trim',
            ],
        ],
        'youtube' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.youtube',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'instagram' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.instagram',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
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
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                            --palette--;' . $l10nPathFE . ':sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                        ],
                    ],
                    'maxitems' => 8,
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
        ],
        'features' => [
            'exclude' => true,
        ],
        'keywords' => [
            'exclude' => true,
            'label' => 'Keywords',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
            ],
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
                    'enabledControls' => [
                        'info' => false,
                        'new' => true,
                        'dragdrop' => true,
                        'sort' => true,
                        'hide' => false,
                        'delete' => true,
                        'localize' => false,
                    ],
                    'levelLinksPosition' => 'top',
                    'showPossibleLocalizationRecords' => false,
                    'showRemovedLocalizationRecords' => false,
                    'showSynchronizationLink' => false,
                    'showAllLocalizationLink' => false,
                ],

            ],

        ],

        'location' => [
            'exclude' => true,
            'label' => $l10nPath . ':tx_events_domain_model_event.location',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_events_domain_model_location',
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
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

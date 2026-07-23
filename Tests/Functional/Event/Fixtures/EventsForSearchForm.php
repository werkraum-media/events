<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Domain\Repository\PageRepository;

return [
    'pages' => [
        [
            'uid' => 1,
            'pid' => 0,
            'title' => 'Root',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
            'slug' => '/',
            'deleted' => 0,
        ],
        [
            'uid' => 10,
            'pid' => 1,
            'title' => 'List Page',
            'doktype' => PageRepository::DOKTYPE_DEFAULT,
            'slug' => '/list/',
            'deleted' => 0,
        ],
        [
            'uid' => 11,
            'pid' => 1,
            'title' => 'Storage for Events',
            'doktype' => PageRepository::DOKTYPE_SYSFOLDER,
            'deleted' => 0,
        ],
    ],
    'tt_content' => [
        [
            'uid' => 10,
            'pid' => 10,
            'CType' => 'werkraummedia_eventlistfiltered',
            'header' => 'Event List',
            'colPos' => 0,
            'sys_language_uid' => 0,
            'pages' => 11,
            'pi_flexform' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                <T3FlexForms>
                    <data>
                        <sheet index="sDEF">
                            <language index="lDEF">
                                <field index="settings.showSearch">
                                    <value index="vDEF">1</value>
                                </field>
                                <field index="settings.categories">
                                    <value index="vDEF">10,11</value>
                                </field>
                            </language>
                        </sheet>
                    </data>
                </T3FlexForms>
            ',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => 1,
            'pid' => 11,
            'title' => 'Stadtmuseum Erfurt',
            'subtitle' => '',
            'teaser' => '',
            'details' => '',
            'categories' => 0,
        ],
        [
            'uid' => 2,
            'pid' => 11,
            'title' => 'Domberg Erfurt',
            'subtitle' => 'Wahrzeichen der Stadt',
            'teaser' => '',
            'details' => '',
            'categories' => 1,
        ],
        [
            'uid' => 3,
            'pid' => 11,
            'title' => 'Goethehaus Weimar',
            'subtitle' => '',
            'teaser' => 'Wohnhaus des Dichters',
            'details' => 'Ausführliche Beschreibung mit Schlagwort Klassik.',
            'categories' => 1,
        ],
    ],
    'sys_category' => [
        [
            'uid' => 10,
            'pid' => 11,
            'title' => 'Museum',
        ],
        [
            'uid' => 11,
            'pid' => 11,
            'title' => 'Kirche',
        ],
    ],
    'sys_category_record_mm' => [
        [
            'uid_local' => 10,
            'uid_foreign' => 3,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting_foreign' => 1,
        ],
        [
            'uid_local' => 11,
            'uid_foreign' => 2,
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting_foreign' => 1,
        ],
    ],
];

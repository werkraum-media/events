<?php

return [
    'pages' => [
        [
            'pid' => '0',
            'uid' => '1',
            'title' => 'Root page',
            'slug' => '1',
        ],
        [
            'pid' => '1',
            'uid' => '2',
            'title' => 'Storage',
            'doktype' => '254',
        ],
    ],
    'sys_category' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example Category 1',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Example Category 2',
        ],
    ],
    'sys_category_record_mm' => [
        [
            'uid_local' => '1',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_event',
        ],
        [
            'uid_local' => '2',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_event',
        ],
    ],
    'sys_file_storage' => [
        [
            'uid' => '1',
            'pid' => '0',
            'tstamp' => '1423209858',
            'crdate' => '1370878372',
            'cruser_id' => '0',
            'deleted' => '0',
            'name' => 'fileadmin/ (auto-created)',
            'description' => 'This is the local fileadmin/ directory. This storage mount has been created automatically by TYPO3.',
            'driver' => 'Local',
            'configuration' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                <T3FlexForms>
                    <data>
                        <sheet index="sDEF">
                            <language index="lDEF">
                                <field index="basePath">
                                    <value index="vDEF">fileadmin/</value>
                                </field>
                                <field index="pathType">
                                    <value index="vDEF">relative</value>
                                </field>
                                <field index="caseSensitive">
                                    <value index="vDEF">1</value>
                                </field>
                            </language>
                        </sheet>
                    </data>
                    </T3FlexForms>
            ',
            'is_browsable' => '1',
            'is_public' => '1',
            'is_writable' => '1',
            'is_online' => '1',
            'processingfolder' => '_processed_',
            'is_default' => '1',
            'auto_extract_metadata' => '1',
        ],
    ],
    'sys_file' => [
        [
            'uid' => '1',
            'pid' => '0',
            'tstamp' => '1371467047',
            'type' => '2',
            'storage' => '1',
            'identifier' => '/user_uploads/example-for-event.gif',
            'extension' => 'gif',
            'mime_type' => 'image/gif',
            'name' => 'ext_icon.gif',
            'sha1' => '359ae0fb420fe8afe1a8b8bc5e46d75090a826b9',
            'size' => '637',
            'creation_date' => '1370877201',
            'modification_date' => '1369407629',
            'last_indexed' => '0',
            'missing' => '0',
            'metadata' => '0',
            'identifier_hash' => '475768e491580fb8b74ed36c2b1aaf619ca5e11d',
            'folder_hash' => 'b4ab666a114d9905a50606d1837b74d952dfd90f',
        ],
        [
            'uid' => '2',
            'pid' => '0',
            'tstamp' => '1371467047',
            'type' => '2',
            'storage' => '1',
            'identifier' => '/user_uploads/example-for-partner.gif',
            'extension' => 'gif',
            'mime_type' => 'image/gif',
            'name' => 'ext_icon.gif',
            'sha1' => '359ae0fb420fe8afe1a8b8bc5e46d75090a826b9',
            'size' => '637',
            'creation_date' => '1370877201',
            'modification_date' => '1369407629',
            'last_indexed' => '0',
            'missing' => '0',
            'metadata' => '0',
            'identifier_hash' => '475768e491580fb8b74ed36c2b1aaf619ca5e11d',
            'folder_hash' => 'b4ab666a114d9905a50606d1837b74d952dfd90f',
        ],
    ],
    'sys_file_metadata' => [
        [
            'uid' => '1',
            'pid' => '0',
            'tstamp' => '1371467047',
            'crdate' => '1371467047',
            'cruser_id' => '1',
            'file' => '1',
        ],
        [
            'uid' => '2',
            'pid' => '0',
            'tstamp' => '1371467047',
            'crdate' => '1371467047',
            'cruser_id' => '1',
            'file' => '2',
        ],
    ],
    'sys_file_reference' => [
        [
            'uid' => '1',
            'pid' => '2',
            'tstamp' => '1373537480',
            'crdate' => '1371484347',
            'cruser_id' => '1',
            'deleted' => '0',
            'hidden' => '0',
            'sys_language_uid' => '0',
            'uid_local' => '1',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => '1',
            'table_local' => 'sys_file',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'tstamp' => '1373537480',
            'crdate' => '1371484347',
            'cruser_id' => '1',
            'deleted' => '0',
            'hidden' => '0',
            'sys_language_uid' => '0',
            'uid_local' => '2',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_partner',
            'fieldname' => 'images',
            'sorting_foreign' => '1',
            'table_local' => 'sys_file',
        ],
    ],
    'tx_events_domain_model_region' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example Region',
        ],
    ],
    'tx_events_domain_model_partner' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example Partner',
            'link' => 'https://example.com',
            'images' => '1',
        ],
    ],
    'tx_events_domain_model_organizer' => [
        [
            'uid' => '1',
            'pid' => '2',
            'name' => 'Example Organizer',
            'street' => 'Example Street 17',
            'district' => '',
            'city' => 'Example Town',
            'zip' => '00101',
            'phone' => '+49 2161 56 36 27 37 48 94 28',
            'web' => 'https://example.com',
            'email' => 'someone@example.com',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Example Event',
            'subtitle' => 'Some further info about event',
            'global_id' => '5540-34',
            'slug' => '5540-34',
            'organizer' => '1',
            'partner' => '1',
            'region' => '1',
            'images' => '0',
            'categories' => '2',
        ],
    ],
    'tx_events_domain_model_date' => [
        [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => '4101372000',
            'end' => '4101377400',
            'canceled' => 'no',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start' => '4101372000',
            'end' => '4101377400',
            'canceled' => 'no',
        ],
    ],
];

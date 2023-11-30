<?php

declare(strict_types=1);

return  [
    'tt_content' => [
        0 => [
            'uid' => '1',
            'pid' => '1',
            'CType' => 'list',
            'list_type' => 'events_eventshow',
            'header' => 'Singleview',
        ],
    ],
    'tx_events_domain_model_event' => [
        0 => [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Title of Event',
            'hidden' => '0',
            'images' => '1',
        ],
    ],
    'sys_file_storage' => [
        [
            'uid' => '1',
            'pid' => '0',
            'tstamp' => '1423209858',
            'crdate' => '1370878372',
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
        0 => [
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
    ],
    'sys_file_reference' => [
        [
            'uid' => '1',
            'pid' => '2',
            'tstamp' => '1373537480',
            'crdate' => '1371484347',
            'deleted' => '0',
            'hidden' => '0',
            'sys_language_uid' => '0',
            'uid_local' => '1',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'images',
            'sorting_foreign' => '1',
        ],
    ],
];

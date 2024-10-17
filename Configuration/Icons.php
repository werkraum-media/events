<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'events-plugin' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:events/Resources/Public/Icons/Extension.svg',
    ],
    'pages-module-events' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:events/Resources/Public/Icons/Folder.svg',
    ],
];

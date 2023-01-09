<?php

defined('TYPO3') or die();

(static function (string $extensionKey, string $tableName) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Events'
    );
})('events', 'sys_template');

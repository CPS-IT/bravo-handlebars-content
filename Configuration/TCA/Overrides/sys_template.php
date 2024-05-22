<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'bravo_handlebars_content',
    'Configuration/TypoScript/',
    'Bravo handlebars content'
);

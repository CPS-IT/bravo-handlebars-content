<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;


final class FileLinkService
{
    public const FILE_PROPERTIES = [
        'title',
        'description',
        'download_name',
        'size',
        'extension',
        'language',
        'copyright',
        'url',
        'accessible',
    ];

    public const FILE_SIZE_UNITS = [
        'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'
    ];


    /**
     * @param mixed $fileReference
     * @param array $properties file properties to return
     * @return array
     */
    public static function resolveFileLik(mixed $fileReference, array $properties = []): array
    {
        $downloadItem = [
            'url' => $fileReference->getPublicUrl(),
        ];
        $properties = !empty($properties) ? $properties : self::FILE_PROPERTIES;

        foreach ($properties as $property) {
            if ($fileReference->hasProperty($property)) {
                $value = $fileReference->getProperty($property);

                if ($property === 'size') {
                    $value = self::formatFileSize($value);
                }

                $downloadItem[GeneralUtility::underscoredToLowerCamelCase($property)] = $value;
            }
        }

        return $downloadItem;
    }

    /**
     * Format a file size in bytes in suitable units.
     * Note: this function might return invalid values for file sizes over 2GB!
     * see https://www.php.net/manual/en/function.filesize.php (Note on Return Values).
     * @param $size
     * @return string
     */
    public static function formatFileSize(int $size): string
    {
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return sprintf("%s %s", number_format(
            $size / (1024 ** $power),
            2,
            '.', // note: we should use the appropriate thousands separator for the curren language
            ','), self::FILE_SIZE_UNITS[$power]);
    }
}

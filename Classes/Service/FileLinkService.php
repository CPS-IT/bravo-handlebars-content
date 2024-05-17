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
    const FILE_PROPERTIES = [
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
                $downloadItem[GeneralUtility::underscoredToLowerCamelCase($property)] =
                    $fileReference->getProperty($property);
            }
        }

        return $downloadItem;
    }

}

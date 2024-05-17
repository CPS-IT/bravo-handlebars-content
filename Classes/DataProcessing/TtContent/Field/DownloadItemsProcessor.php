<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
use TYPO3\CMS\Core\Resource\Collection\StaticFileCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class DownloadItemsProcessor implements FieldProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        $fileReferences = [];

        if (!empty($variables[TtContentRecordInterface::FIELD_FILE_COLLECTIONS])) {
            $collections = $variables[TtContentRecordInterface::FIELD_FILE_COLLECTIONS];
            /** @var StaticFileCollection $collection */
            foreach ($collections as $collection) {
                if (!$collection instanceof StaticFileCollection) {
                    continue;
                }
                $collection->loadContents();
                foreach ($collection->getItems() as $item) {
                    if (!$item instanceof FileReference) {
                        continue;
                    }
                    $fileReferences[] = $item;
                }
            }
        }
        if (!empty($variables[TtContentRecordInterface::FIELD_MEDIA])) {
            $media = $variables[TtContentRecordInterface::FIELD_MEDIA];
            foreach ($media as $medium) {
                if (!$medium instanceof FileReference) {
                    continue;
                }
                $fileReferences[] = $medium;
            }
        }

        $downloadItems = [];
        foreach ($fileReferences as $fileReference) {
            $downloadItems[] = $this->getDownloadProperties($fileReference);
        }
        $variables[$fieldName] = $downloadItems;
        return $variables;
    }

    /**
     * @param mixed $fileReference
     * @return array
     */
    protected function getDownloadProperties(mixed $fileReference): array
    {
        $downloadItem = [
            'url' => $fileReference->getPublicUrl(),
        ];
        $properties = [
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

        foreach ($properties as $property) {
            if ($fileReference->hasProperty($property)) {
                $downloadItem[GeneralUtility::underscoredToLowerCamelCase($property)] =
                    $fileReference->getProperty($property);
            }
        }

        return $downloadItem;
    }
}

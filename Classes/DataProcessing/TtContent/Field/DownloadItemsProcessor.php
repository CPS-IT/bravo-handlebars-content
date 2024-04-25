<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
use TYPO3\CMS\Core\Resource\Collection\StaticFileCollection;
use TYPO3\CMS\Core\Resource\FileReference;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class DownloadItemsProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

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
            $downloadItems[] = [
                'iconDownload' => true,
                'iconPosition' => 'left',
                'label' => $fileReference->getNameWithoutExtension() . $this->getDownloadInfos($fileReference),
                'url' => $fileReference->getPublicUrl()
            ];
        }

        $variables[$fieldName] = $downloadItems;
        return $variables;
    }

    /**
     * @param mixed $fileReference
     * @return string
     */
    protected function getDownloadInfos(mixed $fileReference): string
    {
        // note: we gather some example information
        // todo: add localization for keys, readable file size, check sys_language...
        $info = [
            'language:' => $fileReference->getProperty('language'),
            'extension' => $fileReference->getExtension(),
            'fileSize' => $fileReference->getSize(),
        ];
        $values = [];
        foreach ($info as $key => $value) {
            $values[] = "$key: $value";
        }
        return implode(', ', $values);
    }
}

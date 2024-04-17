<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use TYPO3\CMS\Core\Resource\FileInterface;

trait MetaDataCollectorTrait
{
    public const META_DATA_FIELDS = [
        'alternative' => 'alt',
        'title' => 'title',
        'description' => 'description',
        'caption' => 'caption',
        'copyright' => 'copyright',
        'language' => 'language',
    ];
    
    protected function collectMetaDataFromFile(FileInterface $file): array
    {
        $metaData = [];
        try {
            foreach ($this::META_DATA_FIELDS as $property => $key) {
                $metaData[$key] = $file->hasProperty($property) ? $file->getProperty($property) : '';
            }
        }catch (\Exception) {
            return [];
        }

        return $metaData;
    }
}

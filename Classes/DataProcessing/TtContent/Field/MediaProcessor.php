<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\Dto\FieldProcessorConfiguration;
use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use TYPO3\CMS\Core\Resource\FileInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class MediaProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function __construct(
        protected MediaDataService $mediaDataService,
        protected FileReferencesProcessor $fileReferencesProcessor,
        protected FieldProcessorConfiguration $fieldProcessorConfiguration
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {

        $variables = $this->fileReferencesProcessor->process($fieldName, $data, $variables);
        if(empty($variables[$fieldName])) {
            return $variables;
        }
        $config = $this->fieldProcessorConfiguration->get($fieldName);

        $mediaData = [];
        foreach ($variables[$fieldName] as $file) {
            if(!$file instanceof FileInterface) {
                continue;
            }
            $mediaData[] = $this->mediaDataService->process($file, $config);
        }

        foreach ($mediaData as $media) {
            if (empty($media['type'])) {
               continue;
            }
            $variables[$fieldName][$media['type']][] = $media;
        }
        return $variables;
    }
}

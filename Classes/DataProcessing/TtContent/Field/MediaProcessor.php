<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\Dto\FieldProcessorConfiguration;
use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Frontend\DataProcessing\FilesProcessor;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class MediaProcessor implements FieldProcessorInterface, ContentRendererAwareInterface
{
    use FieldProcessorConfigTrait, ContentRendererTrait;

    public function __construct(
        protected MediaDataService            $mediaDataService,
        protected FilesProcessor              $filesProcessor,
        protected FieldProcessorConfiguration $fieldProcessorConfiguration
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        $filesProcessorConfig = [
            'references.' => [
                'fieldName' => $fieldName,
                'table' => 'tt_content',
            ],
            'as' => $fieldName
        ];
        $variables = $this->filesProcessor->process(
            $this->contentObjectRenderer,
            [],
            $filesProcessorConfig,
            ['data' => $data]
        );
        if (empty($variables[$fieldName])) {
            return $variables;
        }
        $config = $this->fieldProcessorConfiguration->get($fieldName);

        $mediaData = [];
        foreach ($variables[$fieldName] as $file) {
            if (!$file instanceof FileInterface) {
                continue;
            }

            if ($this->mediaDataService instanceof ContentRendererAwareInterface) {
                $this->mediaDataService->setContentObjectRenderer($this->contentObjectRenderer);
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

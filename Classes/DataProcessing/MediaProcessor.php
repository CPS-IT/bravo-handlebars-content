<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Service\MediaDataService;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */


/**
 * Process media  from FAL files or files references
 *
 * Expects an array of array[FileInterface] to be present in:
 * $processedData[
 *   $processorConfiguration['data']
 * ]
 *
 * Example:
 * $processedData [
 *   'files' => [
 *      0 => [FileInterface],
 *      1 => [FileInterface],
 *   ]
 * ]
 * media = handlebarsMedia
 * media {
 *  data = files
 *  settings {
 *     image {
 *       cropVariants {
 *         tablet {
 *           maxWidth = 640
 *         }
 *         mobile {
 *            maxWidth = 320
 *         }
 *       }
 *     }
 *   }
 * }
 */
class MediaProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {

        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->readSettingsFromConfig($processorConfiguration);
        $dataPath = $cObj->stdWrapValue('data', $processorConfiguration, '');

        if (empty($dataPath) || empty($processedData[$dataPath])) {
            return $processedData;
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'files');

        $processedFiles = [];

        foreach ($processedData[$dataPath] as $file) {
            $processedFiles[] = $this->mapMedia($file, $cObj, $this->settings);
        }

        $processedData[$targetVariableName] = $processedFiles;
        return $processedData;
    }

    public function mapMedia(FileInterface $file, ContentObjectRenderer $contentObjectRenderer, array $mediaRendererConfig = []): array
    {
        /** @var MediaDataService $mediaDataService */
        $mediaDataService = GeneralUtility::makeInstance(MediaDataService::class);
        $mediaDataService->setContentObjectRenderer($contentObjectRenderer);
        return $mediaDataService->process($file, $mediaRendererConfig);
    }
}

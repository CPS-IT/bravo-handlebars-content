<?php

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Service\FileLinkService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;



/**
 * Process FAL files or files references
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
 * 10 = handlebarsFileLink
 * 10 {
 *   as = files
 *   data = files
 * }
 *
 * Result:
 * Resolved file links array see: FileLinkService::resolveFileLik
 */
class FileLinkProcessor implements DataProcessorInterface
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
            $processedFiles[] = FileLinkService::resolveFileLik($file);
        }

        $processedData[$targetVariableName] = $processedFiles;
        return $processedData;
    }
}

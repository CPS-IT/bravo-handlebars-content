<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class ContentObjectDataProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

    protected ContentObjectRenderer $cObj;

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array                 $contentObjectConfiguration,
        array                 $processorConfiguration,
        array                 $processedData
    ): array
    {

        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $contentObjectsConf = $processorConfiguration['contentObjects.'] ?? [];
        $contentObjects = [];
        foreach ($contentObjectsConf as $theKey => $theValue) {
            if (!str_contains($theKey, '.')) {
                $conf = $contentObjectsConf[$theKey . '.'] ?? [];
                $contentObjects[$theKey] = $cObj->cObjGetSingle($theValue, $conf);
            }
        }

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, '');

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $contentObjects;
        } else {
            ArrayUtility::mergeRecursiveWithOverrule($processedData, $contentObjects);
        }
        return $processedData;
    }

}



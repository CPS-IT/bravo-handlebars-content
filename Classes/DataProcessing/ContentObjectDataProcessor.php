<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

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

        $this->cObj = $cObj;

        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // Set the target variable
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'ContentObjects');

        $processedData[$targetVariableName] = $this->getContentObjectVariables($processorConfiguration);

        return $processedData;
    }

}



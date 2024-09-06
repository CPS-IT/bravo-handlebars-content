<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Class UnsetDataProcessor
 * Unset some variables
 */
class UnsetDataProcessor implements DataProcessorInterface
{
    protected ?ContentObjectRenderer $contentObjectRenderer = null;
    protected array $processorConfiguration = [];

    public function __construct(protected readonly ContentDataProcessor $contentDataProcessor)
    {
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $this->contentObjectRenderer = $cObj;
        $this->processorConfiguration = $processorConfiguration;

        if (
            isset($processorConfiguration['if.'])
            && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $fields = GeneralUtility::trimExplode(',', $processorConfiguration['fields'] ?? []);
        foreach ($fields as $field) {
            if(!isset($processedData[$field])){
                continue;
            }

            unset($processedData[$field]);
        }

        return $processedData;
    }
}

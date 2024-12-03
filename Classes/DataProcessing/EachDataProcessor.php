<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Process each record in a list of records.
 *
 * Example TypoScript configuration:
 *
 * 10 = handlebarsEach
 * 10 {
 *   sourcePath = records
 *   separator = :
 *   # optional: table to be used for the content object renderer
 *   table = tt_address
 *   dataProcessing {
 *     10 = handlebarsMapFields
 *     10 {
 *       map {
 *        first_name = firstName
 *      }
 *     }
 *   }
 * }
 *
 * where "sourcePath" means the variable containing the list of records.
 */
class EachDataProcessor implements DataProcessorInterface
{
    public const SEPARATOR = ':';
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

        $records = $this->getRecords($processedData);
        $records = $this->processRecords($records);

        return $this->putRecords($processedData, $records);
    }

    protected function putRecords(array $processedData, array $processedRecords): array
    {
        $separator = $this->processorConfiguration['separator'] ?? self::SEPARATOR;
        $targetPath = $this->processorConfiguration['sourcePath'] ?? [];
        return ArrayUtility::setValueByPath($processedData, $targetPath, $processedRecords, $separator);
    }

    protected function getRecords(array $processedData): array
    {
        try {
            $separator = $this->processorConfiguration['separator'] ?? self::SEPARATOR;
            $sourcePath = $this->processorConfiguration['sourcePath'] ?? [];
            $records = ArrayUtility::getValueByPath($processedData, $sourcePath, $separator);
        } catch (MissingArrayPathException) {
            $records = [];
        }

        if (!is_iterable($records)) {
            $records = [];
        }
        return $records;
    }

    protected function processRecords(array $records): array
    {
        $processedRecordVariables = [];
        foreach ($records as $key => $record) {
            $recordContentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $recordContentObjectRenderer->setRequest($this->contentObjectRenderer->getRequest());
            $recordContentObjectRenderer->start($record, $this->processorConfiguration['table'] ?? '');
            $processedRecordVariables[$key] = $record;
            $processedRecordVariables[$key] = $this->contentDataProcessor->process(
                $recordContentObjectRenderer,
                $this->processorConfiguration,
                $processedRecordVariables[$key]
            );
        }
        return $processedRecordVariables;
    }
}

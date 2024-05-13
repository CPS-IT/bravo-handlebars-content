<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use mysql_xdevapi\Exception;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
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

        $records = $this->precessRecords($records);


        $data = $processedData;

        return $processedData;
    }

    protected function getRecords(array $processedData): array
    {
        try {
            $separator = $this->processorConfiguration['separator'] ?? self::SEPARATOR;
            $sourcePath = $this->processorConfiguration['sourcePath'] ?? [];
            $records = ArrayUtility::getValueByPath($processedData, $sourcePath, $separator);
        } catch (\Exception) {
            $records = [];
        }

        if (!is_iterable($records)) {
            $records = [];
        }
        return $records;
    }

    protected function precessRecords(array $records): array
    {
        $processedRecordVariables = [];
        foreach ($records as $key => $record) {
            $recordContentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $recordContentObjectRenderer->setRequest($this->contentObjectRenderer->getRequest());
            $recordContentObjectRenderer->start($record, $this->processorConfiguration['table']);
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

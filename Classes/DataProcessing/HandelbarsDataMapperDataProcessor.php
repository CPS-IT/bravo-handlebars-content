<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use phpDocumentor\Reflection\DocBlock\Tags\Since;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class HandelbarsDataMapperDataProcessor implements DataProcessorInterface
{
    protected string $delimiter = '/';

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // early return nothing to do
        if (empty($processorConfiguration['fielsdMap.'])) {
            return $processedData;
        }

        $fieldsMap = $processorConfiguration['fielsdMap.'];

        $this->setDelimiterFromConfiguration($processorConfiguration);

        foreach ($fieldsMap as $fieldMap) {
            $value = $this->valueFromPath($processedData, $fieldMap['fromPath']);
            $processedData = $this->valueToPath($processedData, $fieldMap['toPath'], $value);
        }

        return $processedData;
    }


    protected function valueFromPath(array $array, array|string $path): mixed
    {
        try {
            return ArrayUtility::getValueByPath($array, $path, $this->delimiter);
        } catch (MissingArrayPathException) {
            return null;
        }
    }

    protected function valueToPath(array $array, array|string $path, mixed $value): mixed
    {
        try {
            return ArrayUtility::setValueByPath($array, $path, $value, $this->delimiter);
        } catch (MissingArrayPathException) {
            return null;
        }
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    protected function setDelimiterFromConfiguration(array $conf): void
    {
        if (!empty($conf['delimiter'])) {
            $this->setDelimiter($conf['delimiter']);
        }
    }

}



<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

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

/**
 * Map array values from source to target
 *
 * Example:
 * $processedData [
 *  publicationDate => foo,
 *  categories => bar
 * ]
 * kicker = handlebarsMapFields
 * kicker {
 *   separator = :
 *   skipEmptyValues = 0
 *   map {
 *     publicationDate = kicker:publicationDate
 *     categories = kicker:categories
 *   }
 * }
 *
 * Return processed data array
 *
 * $processedData [
 *   publicationDate => foo,
 *   categories => bar
 *   kicker => [
 *    publicationDate => foo,
 *    categories => bar
 *   ]
 * ]
 *
 */
class MapFieldsDataProcessor implements DataProcessorInterface
{
    public const SEPARATOR = ':';

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array
    {
        if (
            isset($processorConfiguration['if.'])
            && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $separator = $processorConfiguration['separator'] ?? self::SEPARATOR;
        $map = $processorConfiguration['map.'] ?? [];
        $data = $processedData;

        foreach ($map as $source => $target) {
            if(!ArrayUtility::isValidPath($processedData, $source, $separator)) {
                continue;
            }
            try {

              $value =  ArrayUtility::getValueByPath($processedData, $source, $separator);
                if(!empty($processorConfiguration['skipEmptyValues']) && empty($value) ) {
                    continue;
                }
              $data = ArrayUtility::setValueByPath($data, $target, $value, $separator);
            }catch (MissingArrayPathException) {

            }
        }

        return $data;
    }
}



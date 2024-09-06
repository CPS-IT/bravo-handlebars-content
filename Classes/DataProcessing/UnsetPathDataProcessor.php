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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Remove array items from path
 *
 * Example:
 * $processedData [
 *  publicationDate => foo,
 *  categories => bar
 * ]
 * kicker = handlebarsUnsetPath
 * kicker {
 *   separator = :
 *   paths {
 *     kicker:publicationDate
 *   }
 * }
 *
 * Return processed data array
 *
 * $processedData [
 *    categories => bar
 * ]
 *
 */
class UnsetPathDataProcessor implements DataProcessorInterface
{
    public const SEPARATOR = ':';

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {

        if (
            isset($processorConfiguration['if.'])
            && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $separator = $processorConfiguration['separator'] ?? self::SEPARATOR;
        $paths = $processorConfiguration['paths.'] ?? [];

        foreach ($paths as $path) {
            if (!ArrayUtility::isValidPath($processedData, $path, $separator)) {
                continue;
            }
            try {
                $processedData = ArrayUtility::removeByPath($processedData, $path, $separator);
            } catch (MissingArrayPathException) {

            }
        }

        return $processedData;
    }
}

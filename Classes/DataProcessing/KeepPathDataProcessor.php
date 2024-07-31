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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Remove array items if not in paths.
 *
 * Warning:
 * --------
 * This data processor overrides the $processData array
 *
 * Example:
 * -------
 * ```
 * $processedData [
 *  publicationDate => foo,
 *  categories => bar
 * ]
 * ```
 * Input processed data array
 * ---------------------------
 * ```
 * kicker = handlebarsKeepPath
 * kicker {
 *   separator = :
 *   skipEmptyValues = 0
 *   # CSV list of paths to keep
 *   paths (
 *    kicker:publicationDate,
 *    other:path,
 *   )
 * }
 *```
 * Return processed data array
 * ---------------------------
 * ```
 * $processedData [
 *    publicationDate => foo
 * ]
 * ```
 *
 */
class KeepPathDataProcessor implements DataProcessorInterface
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
        $paths = $processorConfiguration['paths'] ?? '';
        if (empty($paths)) {
            return $processedData;
        }
        $paths = GeneralUtility::trimExplode(',', $paths);
        $data = [];
        foreach ($paths as $path) {
            if (!ArrayUtility::isValidPath($processedData, $path, $separator)) {
                continue;
            }
            try {
                $value = ArrayUtility::getValueByPath($processedData, $path, $separator);
                if (!empty($processorConfiguration['skipEmptyValues']) && empty($value)) {
                    continue;
                }
                $data = ArrayUtility::setValueByPath($data, $path, $value, $separator);
            } catch (MissingArrayPathException) {

            }
        }

        return $data;
    }
}

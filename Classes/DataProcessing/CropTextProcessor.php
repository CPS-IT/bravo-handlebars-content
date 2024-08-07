<?php
/*
 * This file is part of the dena_sitepackage project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Core\Html\HtmlCropper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Sets data platform for handlebars
 * Usage:
 *
 * dataProcessing {
 *   90 = cropText
 *   90 {
 *     field = teaser_text
 *     cropping {
 *       cropNumber = 200
 *       cropEllipsis = ...
 *       stripHtml = 1
 *     }
 *     as = teaser_text
 *   }
 * }
 */
class CropTextProcessor implements DataProcessorInterface
{
    protected $settings = [];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        if (empty($processorConfiguration['field']) || empty($processorConfiguration['cropping.'])) {
            return $processedData;
        }

        $text = $processedData[$processorConfiguration['field']];
        $cropNumber = $processorConfiguration['cropping.']['cropNumber'] ?? 500;
        $cropEllipsis = $processorConfiguration['cropping.']['cropEllipsis'] ?? '...';
        $htmlCropper = GeneralUtility::makeInstance(HtmlCropper::class);
        if (isset($processorConfiguration['cropping.']['stripHtml']) && $processorConfiguration['cropping.']['stripHtml'] === '1') {
            $text = strip_tags($processedData[$processorConfiguration['field']]);
        }
        $croppedText = $htmlCropper->crop($text, $cropNumber, $cropEllipsis, true);

        $as = $processorConfiguration['as'] ?? 'cropped_' . $processorConfiguration['field'];
        $processedData[$as] = $croppedText;

        return $processedData;
    }
}

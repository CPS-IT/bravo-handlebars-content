<?php

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Service\FileLinkService;
use Cpsit\BravoHandlebarsContent\Service\LinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;


/**
 * Process typo link parameter to link
 *
 * Expects a typolink parameter string:
 *
 * Example:
 * 10 = handlebarsLink
 * 10 {
 *   as = link
 *   parameter = 123
 * }
 *
 * Result:
 * Resolved link array see: LinkService::resolveTypoLink
 */
class LinkProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $parameter = $cObj->stdWrapValue('parameter', $processorConfiguration, '');

        if (empty($parameter)) {
            return $processedData;
        }

        $linkService = GeneralUtility::makeInstance(LinkService::class, $cObj);
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'link');
        $linkResult = $linkService->resolveTypoLink($parameter);
        $processedData[$targetVariableName] = $linkService->linkResultToArray($linkResult);

        return $processedData;
    }
}

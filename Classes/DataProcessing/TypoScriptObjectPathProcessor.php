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

use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;


/**
 * Render a typoscript object path
 * Configuration:
 * - as (stdWrap): target variable name, default `content`
 * - typoscriptObjectPath (stdwrap): path to render
 * - if typoscript conditional rendering
 *
 * Example:
 *   dataProcessing {
 *     10 = handlebarsTypoScriptObjectPath
 *     10 {
 *       if.isTrue.field = list_type
 *       as = textHtml
 *       typoscriptObjectPath.field = list_type
 *       typoscriptObjectPath.wrap = tt_content.list.20.|
 *     }
 *   }
 *
 * Result: Plugin rendered content
 *
 */
class TypoScriptObjectPathProcessor implements DataProcessorInterface
{
    protected ContentObjectRenderer $contentObjectRenderer;

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $this->contentObjectRenderer = $cObj;

        if (isset($processorConfiguration['if.'])
            && !$this->contentObjectRenderer->checkIf($processorConfiguration['if.'])
        ) {
            return $processedData;
        }

        $typoscriptObjectPath = $this->contentObjectRenderer->stdWrapValue(
            'typoscriptObjectPath',
            $processorConfiguration,
            ''
        );

        $pathSegments = GeneralUtility::trimExplode('.', $typoscriptObjectPath);
        $lastSegment = (string)array_pop($pathSegments);

        $setup = $this->buildTyposcriptConfArray($typoscriptObjectPath, $pathSegments, $lastSegment);

        $content = $this->renderContentObject($setup, $typoscriptObjectPath, $lastSegment);

        $targetVariableName = $this->contentObjectRenderer->stdWrapValue('as', $processorConfiguration, 'content');

        $processedData[$targetVariableName] = $content;
        return $processedData;
    }

    /**
     * Renders single content object and increases time tracker stack pointer
     */
    protected function renderContentObject(array $setup, string $typoscriptObjectPath, string $lastSegment): string
    {
        $timeTracker = GeneralUtility::makeInstance(TimeTracker::class);
        if ($timeTracker->LR) {
            $timeTracker->push('/f:cObject/', '<' . $typoscriptObjectPath);
        }
        $timeTracker->incStackPointer();
        $content = $this->contentObjectRenderer->cObjGetSingle($setup[$lastSegment], $setup[$lastSegment . '.'] ?? [],
            $typoscriptObjectPath);
        $timeTracker->decStackPointer();
        if ($timeTracker->LR) {
            $timeTracker->pull($content);
        }
        return $content;
    }

    protected function buildTyposcriptConfArray(
        string $typoscriptObjectPath,
        array $pathSegments,
        string $lastSegment
    ): array {
        $setup = $this->getTypoScriptSetup();
        foreach ($pathSegments as $segment) {
            if (!array_key_exists($segment . '.', $setup)) {
                throw new \InvalidArgumentException(
                    'TypoScript object path "' . $typoscriptObjectPath . '" does not exist',
                    1253191023
                );
            }
            $setup = $setup[$segment . '.'];
        }
        if (!isset($setup[$lastSegment])) {
            throw new \InvalidArgumentException(
                'No Content Object definition found at TypoScript object path "' . $typoscriptObjectPath . '"',
                1540246570
            );
        }
        return $setup;
    }

    /**
     * Returns full Frontend TypoScript setup array calculated by FE middlewares.
     */
    protected function getTypoScriptSetup(): array
    {
        $request = $this->contentObjectRenderer->getRequest();
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');

        if (!($frontendTypoScript instanceof FrontendTypoScript)) {
            return [];
        }

        return $frontendTypoScript->getSetupArray();

    }

}



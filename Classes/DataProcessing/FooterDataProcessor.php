<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class FooterDataProcessor implements DataProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {

        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // Set the target variable
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, '@footer');

        $processedData[$targetVariableName] = [
            '@menu-meta' => [
                'items' => [
                    0 => [
                        'url' => 'url.de',
                        'current' => 'current',
                        'page' => 'this is a page test',
                    ],
                    1 => [
                        'url' => 'url.com',
                        'current' => 'current',
                        'page' => 'this is a page test2',
                    ]

                ]
            ]
        ];

        return $processedData;
    }

}



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
class HandelbarsDataMapperDataProcessor implements DataProcessorInterface
{

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

        try {
            # ArrayUtility::getValueByPath();
        }catch (MissingArrayPathException) {

        }

        try {
            # ArrayUtility::setValueByPath($processedData);
        }catch (MissingArrayPathException) {

        }


        // Dummy data
        $data = [
            'spaceBefore' => 'test-spaceBefore',
            'text' => $processedData['data']['bodytext'],
            'headlinesData' => [
                'h3' => [
                    'headline' => 'renderedContent: headline h2'
                ],
            ]
        ];

        return $data;
    }
}



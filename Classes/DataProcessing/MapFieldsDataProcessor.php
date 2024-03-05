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
class MapFieldsDataProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

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
        $this->readSettingsFromConfig($processorConfiguration);
        $data = $processedData;
        if(empty($this->settings['map'])) {
            return $data;
        }
        foreach ($this->settings['map'] as $fieldConfig) {
            if(!ArrayUtility::isValidPath($processedData, $fieldConfig['from'])) {
                continue;
            }
            try {
              $value =  ArrayUtility::getValueByPath($processedData, $fieldConfig['from']);
              $data = ArrayUtility::setValueByPath($data, $fieldConfig['to'], $value);
            }catch (MissingArrayPathException) {

            }

        }

        return $data;
    }
}



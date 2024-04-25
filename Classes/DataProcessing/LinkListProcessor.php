<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class LinkListProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $this->readSettingsFromConfig($processorConfiguration);
        if (empty($this->settings['from'])) {
            return $processedData;
        }
        $as = $this->settings['from'];
        if (!empty($processorConfiguration['as'])) {
            $as = $processorConfiguration['as'];
        }
        $variables = [];

        $linkData = $processedData[$this->settings['from']];
        $items = [];
        foreach ($linkData as $linkItem) {
            $items[] = [
                'linkData' => [
                    'url' => $linkItem['link'],
                    'label' => $linkItem['title']
                ]
            ];
        }
        if (!empty($items)){
            $variables[$as]['items'] = $items;
        }

        return array_merge($processedData, $variables);
    }
}

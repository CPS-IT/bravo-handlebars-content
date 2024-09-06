<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContentDataProcessor;
use Cpsit\BravoHandlebarsContent\Domain\Model\Dto\Link;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class HeadlinesProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        #if (!$variables[TtContentDataProcessor::FIELD_HEADER_LINK] instanceof LinkResultInterface) {
        if (!is_array($variables[TtContentDataProcessor::FIELD_HEADER_LINK])) {
            return $variables;
        }

        $headLine = [
            'headline' => $variables[TtContentDataProcessor::FIELD_HEADER]
        ];

        ArrayUtility::mergeRecursiveWithOverrule(
            $headLine,
            $variables[TtContentDataProcessor::FIELD_HEADER_LINK]
        );

        $link = $variables[TtContentDataProcessor::FIELD_HEADER_LINK];

        $variables[$fieldName] = [
            $variables[TtContentDataProcessor::FIELD_HEADER_LAYOUT] => $headLine
        ];

        return $variables;
    }
}

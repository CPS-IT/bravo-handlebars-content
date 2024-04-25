<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class PlainTextProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function __construct(protected ContentObjectRenderer $contentObjectRenderer)
    {

    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        $value = $data[$fieldName];
        $variables[$fieldName] = $this->contentObjectRenderer->parseFunc(
            trim($value),
            null,
            '< lib.parseFunc'
        );

        return $variables;
    }
}

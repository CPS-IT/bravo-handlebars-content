<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class BodytextProcessor implements FieldProcessorInterface, ContentRendererAwareInterface
{
    use FieldProcessorConfigTrait;
    use ContentRendererTrait;
    public const FIELD_NAME = 'bodytext';

    public function process(string $fieldName, array $data, array $variables): array
    {
        $value = $data[self::FIELD_NAME];
        $variables[$fieldName] = $this->cObj->parseFunc(
            trim($value),
            null,
            '< lib.parseFunc_RTE'
        );

        return $variables;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class ModifierProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        /** @noinspection PhpDuplicateMatchArmBodyInspection */
        $modifier = match ($data[TtContentRecordInterface::FIELD_IMAGE_ORIENT]) {
            0, 1, 2 => 'above', // variants above-left and above-right are ignored
            8, 9, 10 => 'below', // variants below-left and below-right are ignored
            17 => 'float-right',
            18 => 'float-left',
            25 => 'right',
            26 => 'left',
            default => 'above',
        };

        $variables[$fieldName] = $modifier;
        return $variables;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

/**
 * Class PassThrough
 *
 * This processor just returns the value of a given field in the data array
 */
class PassThrough implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        if(isset($data[$fieldName])) {
            $variables[$fieldName] = $data[$fieldName];
        }
        return $variables;
    }
}

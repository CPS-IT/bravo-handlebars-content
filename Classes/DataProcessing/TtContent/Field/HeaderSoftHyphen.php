<?php

declare(strict_types=1);

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
class HeaderSoftHyphen implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        if (isset($data[$fieldName])) {
            $sanitizedData = htmlspecialchars((string)$data[$fieldName], ENT_COMPAT, 'UTF-8', false);
            $variables[$fieldName] = str_replace('--', '&shy;', $sanitizedData);
        }

        return $variables;
    }
}

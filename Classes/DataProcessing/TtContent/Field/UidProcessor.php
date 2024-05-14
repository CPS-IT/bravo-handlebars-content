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

class UidProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        if (isset($data[$fieldName])) {
            $variables[$fieldName] = 'c' . $data[$fieldName];
        }

        return $variables;
    }
}

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
class HeaderLayoutProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public const FIELD_NAME = 'header_layout';

    public function process(string $fieldName, array $data, array $variables): array
    {
        $headerLayout = 2;
        if (!empty($data[self::FIELD_NAME])) {
            $headerLayout = $data[self::FIELD_NAME];
        }

        $variables[$fieldName] = 'h' . $headerLayout;

        return $variables;
    }
}

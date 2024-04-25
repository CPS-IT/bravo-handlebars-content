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
class ContentMediaProcessor implements FieldProcessorInterface
{

    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        // todo: enable if media (video) present
        $variables[$fieldName] = !empty($data[TtContentRecordInterface::FIELD_ASSETS]);
        return $variables;
    }
}

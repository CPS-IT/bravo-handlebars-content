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
class ImageBelowTextProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        // note: variable `imageBelowText` is an additional switch used in the
        // reference template
        $imageBelow = (
            isset($variables['modifier'])
                 && $variables['modifier'] === 'below');

        $variables[$fieldName] = $imageBelow;
        return $variables;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class FrameClassProcessor implements FieldProcessorInterface, ContentRendererAwareInterface
{
    use FieldProcessorConfigTrait;
    use ContentRendererTrait;
    public const FIELD_NAME = 'frame_class';

    public function process(string $fieldName, array $data, array $variables): array
    {
        $value = $data[self::FIELD_NAME];
        $variables[$value] = true;
        return $variables;
    }
}

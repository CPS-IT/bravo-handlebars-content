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
class SpaceBeforeProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public const DEFAULT_CLASS = 'u-space-top:default';

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        $spaceBeforeClass = self::DEFAULT_CLASS;
        if (!empty($data[$fieldName]) && is_string($data[$fieldName])) {
            $spaceBeforeClass = $data[$fieldName];
        }

        $variables[$fieldName] = $spaceBeforeClass;
        return $variables;
    }
}

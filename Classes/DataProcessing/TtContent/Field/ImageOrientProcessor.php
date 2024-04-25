<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Exception\InvalidValueException;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class ImageOrientProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public const ABOVE_CENTER = 'above-center';
    public const ABOVE_RIGHT = 'above-right';
    public const ABOVE_LEFT = 'above-left';
    public const BELOW_CENTER = 'below-center';
    public const BELOW_RIGHT = 'below-right';
    public const BELOW_LEFT = 'below-left';
    public const IN_TEXT_RIGHT = 'in-text-right';
    public const IN_TEXT_LEFT = 'in-text-left';
    public const BESIDE_TEXT_RIGHT = 'beside-text-right';
    public const BESIDE_TEXT_LEFT = 'beside-text-left';

    public const DEFAULT_VALUE = 0;
    public const DEFAULT_ORIENTATION = self::ABOVE_CENTER;

    public const VALUE_MAP = [
        0 => self::ABOVE_CENTER, // equates "above center"
        1 => self::ABOVE_RIGHT,
        2 => self::BELOW_RIGHT,
        8 => self::BELOW_CENTER,
        9 => self::BELOW_RIGHT,
        10 => self::BELOW_LEFT,
        17 => self::IN_TEXT_RIGHT,
        18 => self::IN_TEXT_LEFT,
        25 => self::BESIDE_TEXT_RIGHT,
        26 => self::BESIDE_TEXT_LEFT,
    ];

    public const ERROR_INVALID_VALUE_MESSAGE = 'Invalid value `%s` for field %s.';

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {

        $imageOrientation = self::DEFAULT_VALUE;
        if (isset($data[$fieldName]) && is_int($data[$fieldName])) {
            $imageOrientation = $data[$fieldName];
        }

        if(!array_key_exists($imageOrientation, self::VALUE_MAP)) {
            throw new InvalidValueException(
                sprintf(
                    self::ERROR_INVALID_VALUE_MESSAGE,
                    (string)$imageOrientation,
                    $fieldName
                )
            );
        }
        $variables[$fieldName] = self::VALUE_MAP[$imageOrientation];
        return $variables;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Exception\InvalidValueException;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class ImageOrientProcessor implements FieldProcessorInterface
{
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

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;

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

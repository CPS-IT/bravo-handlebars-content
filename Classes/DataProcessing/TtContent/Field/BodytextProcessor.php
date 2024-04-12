<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
class BodytextProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;
    public const FIELD_NAME = 'bodytext';

    public function __construct(protected ContentObjectRenderer $contentObjectRenderer)
    {

    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        $value = $data[self::FIELD_NAME];
        $variables[$fieldName] = $this->contentObjectRenderer->parseFunc(
            trim($value),
            null,
            '< lib.parseFunc_RTE'
        );

        return $variables;
    }
}

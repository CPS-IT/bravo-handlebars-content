<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;


use TYPO3\CMS\Core\Utility\ArrayUtility;
use Cpsit\BravoHandlebarsContent\DataProcessing\Map\FieldMap;

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
trait FieldMappingTrait
{
    public function map(array $variables): array {
        /** @var FieldMap $fieldMap */
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            if(ArrayUtility::isValidPath($variables, $fieldMap->sourcePath, $fieldMap->delimiter)) {
                $value = ArrayUtility::getValueByPath(
                    $variables,
                    $fieldMap->sourcePath,
                    $fieldMap->delimiter
                );
                $variables = ArrayUtility::setValueByPath(
                    $variables,
                    $fieldMap->targetPath,
                    $value,
                    $fieldMap->delimiter
                );
            }
        }

        return $variables;
    }

}

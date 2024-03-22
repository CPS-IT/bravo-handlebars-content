<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
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
class LightboxImageProcessor implements FieldProcessorInterface
{
    public const FIELD_NAME = 'bodytext';

    public function process(string $fieldName, array $data, array $variables): array
    {
        if(
            empty($variables[TtContentRecordInterface::FIELD_IMAGE_ZOOM])
            || !(bool)$variables[TtContentRecordInterface::FIELD_IMAGE_ZOOM]
            || empty($variables[TtContentRecordInterface::FIELD_ASSETS]['@picture'])
        ) {
            // nothing to do
            return $variables;
        }

        $sources = $variables[TtContentRecordInterface::FIELD_ASSETS]['@picture'];
        if(!empty($sources['sourceL'])) {
            $variables[$fieldName] = $sources['sourceL'];
        }
        if(!empty($sources['sourceXl'])) {
            $variables[$fieldName] = $sources['sourceXl'];
        }

        return $variables;
    }
}

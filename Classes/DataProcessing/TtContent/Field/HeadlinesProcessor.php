<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContentDataProcessor;
use Cpsit\BravoHandlebarsContent\Domain\Model\Dto\Link;

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
class HeadlinesProcessor implements FieldProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        if (!$variables[TtContentDataProcessor::FIELD_HEADER_LINK] instanceof Link) {
            return $variables;
        }

        $link = $variables[TtContentDataProcessor::FIELD_HEADER_LINK];
        $variables[$fieldName] = [
            $variables[TtContentDataProcessor::FIELD_HEADER_LAYOUT] => [
                'headline' => $variables[TtContentDataProcessor::FIELD_HEADER],
                'url' => $link->url,
                'target' => $link->target
            ]
        ];

        return $variables;
    }
}
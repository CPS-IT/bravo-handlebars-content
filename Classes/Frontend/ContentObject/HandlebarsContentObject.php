<?php

namespace Cpsit\BravoHandlebarsContent\Frontend\ContentObject;

use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;
use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;

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
class HandlebarsContentObject extends AbstractContentObject
{
    use ProcessorVariablesTrait;

    public function __construct(
        protected ContentDataProcessor $contentDataProcessor,
    )
    {
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function render($conf = []): array
    {
        if (!is_array($conf)) {
            $conf = [];
        }
        $this->readSettingsFromConfig($conf);

        $variables = $this->getContentObjectVariables($conf);
        $variables = $this->contentDataProcessor->process($this->cObj, $conf, $variables);

        return array_merge_recursive(
            $variables,
            $this->settings
        );
    }


}

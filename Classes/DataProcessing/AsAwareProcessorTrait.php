<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
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
trait AsAwareProcessorTrait
{
    private readonly ContentObjectRenderer $contentObjectRenderer;

    /**
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException
     */
    public function determineTargetVariableName(array $processorConfiguration): string
    {
        $variableName = $this->contentObjectRenderer->stdWrapValue(
            'as',
            $processorConfiguration
        );
        if (empty($variableName)) {
            throw new InvalidConfigurationException(
                sprintf('Missing configuration "as" in %s', get_class($this)),
                1713766921
            );
        }

        return $variableName;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;

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
class LocalizationDataProcessor implements DataProcessorInterface
{
    use IfAwareProcessorTrait,
        AsAwareProcessorTrait,
        LocalizationTrait,
        ProcessorVariablesTrait;

    public function __construct(
        private readonly ContentObjectRenderer  $contentObjectRenderer,
        private readonly LanguageServiceFactory $languageServiceFactory
    )
    {
    }


    /**
     * @throws InvalidConfigurationException
     * @throws ContentRenderingException
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        if (!$this->shouldProcess($processorConfiguration)) {
            return $processedData;
        }

        $targetVariableName = $this->determineTargetVariableName($processorConfiguration);
        $processedData[$targetVariableName] = $this->getLocalizedStrings(
            $this->getTypoScriptToPlainArray($processorConfiguration)
        );
        return $processedData;
    }

}

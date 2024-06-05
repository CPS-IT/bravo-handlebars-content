<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
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
        AsAwareProcessorTrait;

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
        $this->assertValidSource($processorConfiguration);
        $sources = $processorConfiguration['sources.'];
        $request = $this->contentObjectRenderer->getRequest();
        $languageService = $this->languageServiceFactory->createFromSiteLanguage(
            $request->getAttribute('language')
            ?? $request->getAttribute('site')->getDefaultLanguage()
        );

        $labels = [];
        foreach ($sources as $source) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $labels = array_merge($labels, $languageService->getLabelsFromResource($source));
        }

        if(!empty($processorConfiguration['includePattern'])) {
            $pattern = $processorConfiguration['includePattern'];
            $labels = array_filter($labels,
                static function ($key) use ($pattern){
                    return preg_match($pattern, $key);
                }, ARRAY_FILTER_USE_KEY
            );
        }

        if (!empty($processorConfiguration['splitChar'])) {
            $splitChar = $processorConfiguration['splitChar'];
            $labels = ArrayUtility::unflatten($labels, $splitChar);
        }

        $processedData[$targetVariableName] = $labels;
        return $processedData;
    }

    /**
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException
     */
    protected function assertValidSource(array $processorConfiguration): void
    {
        if (empty($processorConfiguration['sources.'] || !is_array($processorConfiguration['sources']))) {
            throw new InvalidConfigurationException(
                'Missing or invalid configuration key `sources`',
                1717584873
            );
        }
    }

}

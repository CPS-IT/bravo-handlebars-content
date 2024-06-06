<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
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
trait LocalizationTrait
{
    private readonly ContentObjectRenderer $contentObjectRenderer;
    private readonly LanguageServiceFactory $languageServiceFactory;

    /**
     * @throws InvalidConfigurationException
     * @throws ContentRenderingException
     */
    public function getLocalizedStrings(array $config): array
    {
        $this->assertValidSource($config);
        $sources = $config['sources'];
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

        if(!empty($config['includePattern'])) {
            $pattern = $config['includePattern'];
            $labels = array_filter($labels,
                static function ($key) use ($pattern){
                    return preg_match($pattern, $key);
                }, ARRAY_FILTER_USE_KEY
            );
        }

        if (!empty($config['splitChar'])) {
            $splitChar = $config['splitChar'];
            $labels = ArrayUtility::unflatten($labels, $splitChar);
        }

        return $labels;
    }

    /**
     * @throws InvalidConfigurationException
     */
    protected function assertValidSource(array $config): void
    {
        if (empty($config['sources'] || !is_array($config['sources']))) {
            throw new InvalidConfigurationException(
                'Missing or invalid configuration key `sources`',
                1717584873
            );
        }
    }

}

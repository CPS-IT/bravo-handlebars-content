<?php

namespace Cpsit\BravoHandlebarsContent\Frontend\ContentObject;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use Fr\Typo3Handlebars\Renderer\HandlebarsRenderer;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
class HandlebarsTemplateContentObject extends AbstractContentObject
{
    /**
     * @var array|mixed[]
     */
    private array $settings = [];

    public function __construct(
        protected AssetCollector $assetCollector,
        protected ContentDataProcessor $contentDataProcessor,
        protected HandlebarsRenderer   $renderer,
    ) {
    }

    public function render($conf = [])
    {

        if (!is_array($conf)) {
            $conf = [];
        }
        $this->assertSettingsFromConfig($conf);
        $this->addPageAssets($conf);

        $variables = $this->getContentObjectVariables($conf);
        $variables = $this->contentDataProcessor->process($this->cObj, $conf, $variables);

        $variables = array_merge_recursive(
            $variables,
            $this->settings
        );


        return $this->renderer->render(
            $this->resolveTemplateName($conf),
            $variables
        );
    }

    protected function getContentObjectVariables(array $conf): array
    {
        $variables = [];
        $reservedVariables = ['data', 'current'];
        // Accumulate the variables to be process and loop them through cObjGetSingle
        $variablesToProcess = (array)($conf['variables.'] ?? []);
        foreach ($variablesToProcess as $variableName => $cObjType) {
            if (is_array($cObjType)) {
                continue;
            }
            if (!in_array($variableName, $reservedVariables)) {
                $cObjConf = $variablesToProcess[$variableName . '.'] ?? [];
                $variables[$variableName] = $this->cObj->cObjGetSingle($cObjType, $cObjConf,
                    'variables.' . $variableName);
            } else {
                throw new \InvalidArgumentException(
                    'Cannot use reserved name "' . $variableName . '" as variable name in FLUIDTEMPLATE.',
                    1288095720
                );
            }
        }
        $variables['data'] = $this->cObj->data;
        $variables['current'] = $this->cObj->data[$this->cObj->currentValKey ?? null] ?? null;
        return $variables;

    }

    /**
     * Resolve template name
     *
     * @param array $conf With possibly set file resource
     * @throws \InvalidArgumentException
     */
    protected function resolveTemplateName(array $conf): string
    {
        if (!empty($conf['templateName']) || !empty($conf['templateName.'])) {
            $templateName = $this->cObj->stdWrapValue('templateName', $conf ?? []);
        }

        if (empty($templateName)) {
            throw new ContentRenderingException(
                'Could not find template name for ' . $conf['templateName'],
                1437420865
            );
        }

        return $templateName;
    }

    protected function assertSettingsFromConfig(array $conf): void
    {
        $settings = [];
        if (isset($conf['settings.'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $this->settings = $typoScriptService->convertTypoScriptArrayToPlainArray($conf['settings.']);
        }

    }

    protected function addPageAssets(array $conf): void
    {
        $assetsConfig = [];
        if(!empty($conf['assets.'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $assetsConfig = $typoScriptService->convertTypoScriptArrayToPlainArray($conf['assets.']);
        }

        if(!empty($assetsConfig['javaScript'])) {
            foreach ($assetsConfig['javaScript'] as $identifier=>$item)
            {
                $options = [];
                if(empty($item['source']) || !is_string($item['source'])) {
                    $message = sprintf('missing key "source" in configuration assets.javaScript.%s for %s.', $identifier, get_class($this));
                    throw new InvalidConfigurationException(
                        $message,
                        1709302386
                    );
                }
                $source = $item['source'];

                if (!empty($item['options'])) {
                    $options = $item['options'];
                }
                // todo: we might need to pass additional parameters here
                $this->assetCollector->addJavaScript($identifier, $source, $options);
            }
        }
    }

}

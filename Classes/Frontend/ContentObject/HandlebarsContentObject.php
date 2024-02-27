<?php

namespace Cpsit\BravoHandlebarsContent\Frontend\ContentObject;

use Fr\Typo3Handlebars\Renderer\HandlebarsRenderer;
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
class HandlebarsContentObject extends AbstractContentObject
{
    protected $view;

    public function __construct(
        protected ContentDataProcessor $contentDataProcessor,
        protected HandlebarsRenderer $renderer
    ) {
    }

    public function render($conf = [])
    {

        $parentView = $this->view;
        $this->initializeViewInstance();
        if (!is_array($conf)) {
            $conf = [];
        }
        $this->setTemplate($conf);

        $variables = $this->getContentObjectVariables($conf);
        $variables = $this->contentDataProcessor->process($this->cObj, $conf, $variables);



        /*
                if (isset($conf['settings.'])) {
                    $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
                    $settings = $typoScriptService->convertTypoScriptArrayToPlainArray($conf['settings.']);
                    $variables = array_merge_recursive($variables, $settings);
                }
        */
        $templateName = $this->resolveTemplateName($conf);

        $content = $this->renderer->render(
            $templateName,
            $variables
        );
        /**
         * $this->setFormat($conf);
         *
         * $this->setLayoutRootPath($conf);
         * $this->setPartialRootPath($conf);
         * $this->setVariables($conf);
         * $this->assignSettings($conf);
         * $variables = $this->getContentObjectVariables($conf);
         * $variables = $this->contentDataProcessor->process($this->cObj, $conf, $variables);
         *
         * $this->view->assignMultiple($variables);
         *
         * $this->renderFluidTemplateAssetsIntoPageRenderer($variables);
         * $content = $this->renderTemplateView();
         * $content = $this->applyStandardWrapToRenderedContent($content, $conf);
         *
         * $this->view = $parentView;
         */
        return $content;
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

    protected function initializeViewInstance(): void
    {
        //$this->renderer = GeneralUtility::makeInstance(HandlebarsRenderer::class);
    }

    protected function setFormat(array $conf): void
    {

    }

    protected function setTemplate(array $conf): void
    {
    }

    protected function setLayoutRootPath(array $conf): void
    {

    }

    protected function setPartialRootPath(array $conf): void
    {

    }

    protected function setVariables(array $conf): void
    {

    }

    protected function renderTemplateView(): void
    {

    }

}

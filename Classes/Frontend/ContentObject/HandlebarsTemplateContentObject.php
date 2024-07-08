<?php

namespace Cpsit\BravoHandlebarsContent\Frontend\ContentObject;

use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;
use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use Fr\Typo3Handlebars\Renderer\HandlebarsRenderer;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class HandlebarsTemplateContentObject extends AbstractContentObject
{
    use ProcessorVariablesTrait;

    public function __construct(
        protected AssetCollector $assetCollector,
        protected ContentDataProcessor $contentDataProcessor,
        protected HandlebarsRenderer $renderer,
    ) {
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function render($conf = []): string
    {


        if (!is_array($conf)) {
            $conf = [];
        }
        $this->readSettingsFromConfig($conf);
        $this->addPageAssets($conf);

        $variables = $this->getContentObjectVariables($conf);
        $variables = $this->contentDataProcessor->process($this->cObj, $conf, $variables);

        $variables = array_merge_recursive(
            $variables,
            $this->settings
        );

        $defaultData = [];
        $variableNames = empty($conf['defaultDataVariables']) ? [] : GeneralUtility::trimExplode(',',
            $conf['defaultDataVariables']);
        foreach ($variableNames as $variableName) {
            if (empty($variables[$variableName])) {
                continue;
            }
            $defaultData[$variableName] = $variables[$variableName];
        }
        $this->renderer->setDefaultData($defaultData);

        return $this->renderer->render(
            $this->resolveTemplateName($conf),
            $variables
        );
    }

    /**
     * Resolve template name
     *
     * @param array $conf With possibly set file resource
     * @throws \InvalidArgumentException
     * @throws InvalidConfigurationException
     */
    protected function resolveTemplateName(array $conf): string
    {
        if (!empty($conf['templateName']) || !empty($conf['templateName.'])) {
            $templateName = $this->cObj->stdWrapValue('templateName', $conf ?? []);
        }

        if (empty($templateName)) {
            throw new InvalidConfigurationException(
                'Could not find template name for ' . $conf['templateName'],
                1709328957
            );
        }

        return $templateName;
    }


    protected function addPageAssets(array $conf): void
    {
        $assets = [];
        if (!empty($conf['assets.'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $assets = $typoScriptService->convertTypoScriptArrayToPlainArray($conf['assets.']);
        }

        foreach ($assets as $assetType => $assetConfig) {
            foreach ($assetConfig as $identifier => $item) {
                if (empty($item['source']) || !is_string($item['source'])) {
                    $message = sprintf(
                        'Missing key "source" in configuration assets.%s.%s for %s.',
                        $assetType,
                        $identifier,
                        get_class($this));
                    throw new InvalidConfigurationException(
                        $message,
                        1709302386
                    );
                }
                $source = $item['source'];

                $options = [
                    'priority' => !empty($item['options']['priority']) && (bool)$item['options']['priority'],
                    'useNonce' => !empty($item['options']['useNonce']) && (bool)$item['options']['useNonce'],
                ];

                $attributes = [];
                if (!empty($item['attributes']) && is_array($item['attributes'])) {
                    $attributes = $item['attributes'];
                }

                if ($assetType == 'javaScript') {
                    // boolean attributes shall output attr="attr" if set
                    foreach (['async', 'defer', 'nomodule'] as $_attr) {
                        if ($attributes[$_attr] ?? false) {
                            $attributes[$_attr] = $_attr;
                        }
                    }

                    $this->assetCollector->addJavaScript($identifier, $source, $attributes, $options);
                }

                if ($assetType == 'css') {
                    // boolean attributes shall output attr="attr" if set
                    if ($attributes['disabled'] ?? false) {
                        $attributes['disabled'] = 'disabled';
                    }
                    $this->assetCollector->addStyleSheet($identifier, $source, $attributes, $options);
                }
            }
        }
    }
}

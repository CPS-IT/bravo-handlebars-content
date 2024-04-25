<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\CpsUtility\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
trait ProcessorVariablesTrait
{
    protected array $settings = [];

    // keys for configuration
    public const KEY_FIELDS = 'fields';

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
                    'Cannot use reserved name "' . $variableName . '" as variable name.',
                    1288095720
                );
            }
        }

        $variables['data'] = $this->cObj->data;
        $variables['current'] = $this->cObj->data[$this->cObj->currentValKey ?? null] ?? null;

        return $variables;
    }

    protected function readSettingsFromConfig(array $conf): void
    {
        if (isset($conf['settings.'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $this->settings = $typoScriptService->convertTypoScriptArrayToPlainArray($conf['settings.']);
        }

        if (property_exists($this, 'requiredKeys') && defined('static::DEFAULT_FIELDS')) {
            $this->requiredKeys = array_keys(static::DEFAULT_FIELDS);
        }

        if(!empty($this->settings[self::KEY_FIELDS])) {
            $this->requiredKeys = GeneralUtility::trimExplode(',', $this->settings[self::KEY_FIELDS]);
        }
    }

    protected function parseTypoScriptStdWrap(array $settings, ContentObjectRenderer $cObj): array
    {
        /** @var TypoScriptUtility $typoScriptUtility */
        $typoScriptUtility = GeneralUtility::makeInstance(TypoScriptUtility::class);
        return $typoScriptUtility->stdWrapParser($settings, $cObj);
    }

}

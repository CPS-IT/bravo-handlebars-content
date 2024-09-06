<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Adds more properties needed to render the language menu with handlebars
 *
 * availableLanguagesCount: number of available languages
 * languageAvailability: true or false if availableLanguagesCount > 1
 */
class LanguageMenuProcessor extends \TYPO3\CMS\Frontend\DataProcessing\LanguageMenuProcessor
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        // Process menu
        $processedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        $processedData[$this->menuTargetVariableName] = $this->addMenuLevels($processedData[$this->menuTargetVariableName]);

        $countAvailableLanguages = $this->countAvailableLanguages($processedData[$this->menuTargetVariableName]);

        $processedData['available'] = $countAvailableLanguages > 1;
        $processedData['availableLanguagesCount'] = $countAvailableLanguages;

        return $processedData;
    }

    protected function countAvailableLanguages(array $menu)
    {
        return array_count_values(array_column($menu, 'available'))[1];
    }

    /**
     * Add menu items levels
     */
    protected function addMenuLevels(array $menu, int $level = 0): array
    {
        foreach ($menu as $key => &$menuItem) {
            if (isset($menuItem['children']) && is_array($menuItem['children'])) {
                $menuItem['children'] = $this->addMenuLevels($menuItem['children'], $level++);
            }
            $menuItem['level'] = $level;
        }
        return $menu;
    }
}

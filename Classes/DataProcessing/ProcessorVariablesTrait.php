<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\CpsUtility\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
trait ProcessorVariablesTrait
{
    protected array $settings = [];

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
    }

    protected function parseTypoScriptStdWrap(array $settings, ContentObjectRenderer $cObj): array
    {
        /** @var TypoScriptUtility $typoScriptUtility */
        $typoScriptUtility = GeneralUtility::makeInstance(TypoScriptUtility::class);
        return $typoScriptUtility->stdWrapParser($settings, $cObj);
    }

}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\ProcessorVariablesTrait;

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
class LinkListProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $this->readSettingsFromConfig($processorConfiguration);
        if (empty($this->settings['from'])) {
            return $processedData;
        }
        $as = $this->settings['from'];
        if (!empty($processorConfiguration['as'])) {
            $as = $processorConfiguration['as'];
        }
        $variables = [];

        $linkData = $processedData[$this->settings['from']];
        $items = [];
        foreach ($linkData as $linkItem) {
            $items[] = [
                'linkData' => [
                    'url' => $linkItem['link'],
                    'label' => $linkItem['title']
                ]
            ];
        }
        if (!empty($items)){
            // todo: generate headlinesData
            $variables[$as]['items'] = $items;
        }

        return array_merge($processedData, $variables);
    }
}

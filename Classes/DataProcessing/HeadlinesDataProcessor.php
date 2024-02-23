<?php

namespace Cpsit\BravoHandelbarPage\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

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
class HeadlinesDataProcessor implements DataProcessorInterface
{

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array
    {
        $foo = 'bar';
        $processedData['data']['@headlines'] = [
            'title' => 'Foo Bar',
            'headlinesData' => [
                'h2' => [
                    'headline' => $processedData['data']['header'],
                ],
                'tag' => 'h2', // todo: read from header_layout using mapping
                'subheadline' => $processedData['data']['subheader'],
            ],
            'text' => "Text"
        ];

        return $processedData;
    }
}

/**
 * {
 * "title": "Deutsche Energie-Agentur (dena)",
 * "publicPath": "/assets/dena-frontend/base/",
 * "headlinesData": {
 * "h2": {
 * "headline": "Text"
 * }
 * },
 * "spaceBefore": "u-space-top:default",
 * "text": "Es ist ein lang erwiesener Fakt, dass ein Leser vom Text abgelenkt wird, wenn er sich ein Layout ansieht. Der Punkt Lorem Ipsum zu nutzen ist, dass es mehr oder weniger die normale Anordnung von Buchstaben darstellt."
 * } */

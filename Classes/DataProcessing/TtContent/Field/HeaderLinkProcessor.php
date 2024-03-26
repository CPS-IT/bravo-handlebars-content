<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\LinkService;
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
readonly class HeaderLinkProcessor implements FieldProcessorInterface
{
    private LinkService $linkService;
    public function __construct(ContentObjectRenderer $contentObjectRenderer) {
        $this->linkService = GeneralUtility::makeInstance(
            LinkService::class,
            $contentObjectRenderer
        );
    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        $typoLink = $data['header_link'] ?? '';
        $variables[$fieldName]  = $this->linkService->resolveTypoLink($typoLink);
        return $variables;
    }
}

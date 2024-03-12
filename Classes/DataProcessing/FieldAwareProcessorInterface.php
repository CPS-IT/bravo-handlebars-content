<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;

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
interface FieldAwareProcessorInterface
{
    /**
     * @throws InvalidClassException
     */
    public function instantiateFieldProcessor(
        string                $processorClass,
        ContentObjectRenderer $contentObjectRenderer
    ): FieldProcessorInterface;

    /**
     * @param array<string> $requiredKeys An array of key required for processing
     * @throws InvalidClassException
     */
    public function processFields(array $requiredKeys, ContentObjectRenderer $cObj, array $data): array;
}

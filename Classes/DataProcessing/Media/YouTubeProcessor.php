<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use TYPO3\CMS\Core\Resource\FileInterface;

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
class YouTubeProcessor implements MediaProcessorInterface
{

    public function canProcess(FileInterface $file): bool
    {
        // TODO: Implement canProcess() method.
    }
    
    public function process(FileInterface $file, array $config): array
    {
        // TODO: Implement process() method.
    }
}

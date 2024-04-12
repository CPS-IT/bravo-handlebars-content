<?php

namespace Cpsit\BravoHandlebarsContent\Service;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\AudioProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\ImageProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\NullProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\VimeoProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\YouTubeProcessor;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class MediaDataService
{
    protected array $classNames = [
        ImageProcessor::class,
        AudioProcessor::class,
        YouTubeProcessor::class,
        VimeoProcessor::class,
        // note: NullProcessor must be the last one
        NullProcessor::class
    ];

    /** @var array<MediaProcessorInterface> */
    protected array $processorInstances = [];

    public function __construct()
    {
        foreach ($this->classNames as $className) {
            $this->processorInstances[] = GeneralUtility::makeInstance($className);
        }
    }

    /**
     * Processes a file according to its type.
     *
     * @param FileInterface $file
     * @param array $config Optional configuration like width, height or additional attributes
     * @return array Data for template
     */
    public function process(FileInterface $file, array $config = []): array
    {
        return $this->getProcessor($file)->process($file, $config);
    }

    protected function getProcessor(FileInterface $file): MediaProcessorInterface
    {

        foreach ($this->processorInstances as $processorInstance) {
            if (!$processorInstance->canProcess($file)) {
                continue;
            }

            return $processorInstance;
        }

    }
}

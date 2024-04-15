<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\AbstractFile;
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
class AudioProcessor implements MediaProcessorInterface
{
    public const ALLOWED_MIME_TYPES = [
        'audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/ogg'
    ];

    public function canProcess(FileInterface $file): bool
    {
        return (
            in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)
        );
    }

    public function process(FileInterface $file, array $config = []): array
    {
        // TODO: Implement process() method.
        return [
            'attributes' => [
                'autoplay' => empty($file->getProperty('autoplay')) || (bool) $file->getProperty('autoplay'),
                'controls' => empty($config['controls']) || (bool)$config['controls'],
                'loop' => !empty($config['loop']) && (bool)$config['bool']
            ],
            'src' => $file->getPublicUrl(),
            'type' => $file->getMimeType()
        ];
    }
}

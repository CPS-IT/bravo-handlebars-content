<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\Service\ImageService;

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
class ImageProcessor implements MediaProcessorInterface
{
    public const KEY_ALT = 'alt';
    public const KEY_ALTERNATIVE = 'alternative';
    public const KEY_CAPTION = 'caption';
    public const KEY_COPYRIGHT = 'copyright';
    public const KEY_COPYRIGHT_DATA = 'copyrightData';
    public const KEY_CROP_VARIANTS = 'cropVariants';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_HEIGHT = 'height';
    public const KEY_SRC = 'src';
    public const KEY_TITLE = 'title';
    public const KEY_TYPE = 'type';
    public const KEY_VARIANTS = 'variants';
    public const KEY_WIDTH = 'width';

    public const MEDIA_TYPE = 'image';

    public const DEFAULT_CONFIG = [
        self::KEY_CROP_VARIANTS => [
            'default' => []
        ]
    ];

    public function __construct(protected ImageService $imageService)
    {

    }


    public function canProcess(FileInterface $file): bool
    {
        return (
            $file instanceof FileReference
            && $file->getType() === AbstractFile::FILETYPE_IMAGE
        );
    }

    /**
     * @throws \JsonException
     */
    public function process(FileInterface $file, array $config = []): array
    {
        // default crop variant
        $cropVariants = self::DEFAULT_CONFIG[self::KEY_CROP_VARIANTS];

        if (!empty($config[self::MEDIA_TYPE][self::KEY_CROP_VARIANTS])) {
            $cropVariants = $config[self::MEDIA_TYPE][self::KEY_CROP_VARIANTS];
        }

        $imageData = [
            self::KEY_TYPE => self::MEDIA_TYPE,
            self::KEY_ALTERNATIVE => $this->getAlternative($file),
            self::KEY_CAPTION => $this->getCaption($file),
            self::KEY_COPYRIGHT_DATA => $this->getCopyRightData($file),
            self::KEY_VARIANTS => []
        ];

        foreach ($cropVariants as $variant => $variantConfig) {
            $imageData[self::KEY_VARIANTS][$variant] = $this->processCropVariant($variant, $variantConfig, $file);
        }

        return $imageData;
    }

    /**
     * @param string $cropVariant
     * @param array $config
     * @param FileInterface $file
     * @return array
     */
    protected function processCropVariant(string $cropVariant, array $config, FileInterface $file): array
    {
        $cropString = $file instanceof FileReference ? $file->getProperty('crop') : '';
        $cropVariantCollection = CropVariantCollection::create((string)$cropString);
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);


        $config['crop'] = $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($file);

        $image = $this->imageService->applyProcessingInstructions($file, $config);

        return [
            self::KEY_SRC => $this->imageService->getImageUri($image),
            self::KEY_WIDTH => $image->getProperty(self::KEY_WIDTH),
            self::KEY_HEIGHT => $image->getProperty(self::KEY_HEIGHT)
        ];
    }

    protected function getAlternative(FileInterface $file): string
    {
        $alternative = '';
        if ($file->hasProperty(self::KEY_ALTERNATIVE)) {
            $alternative = $file->getProperty(self::KEY_ALTERNATIVE);
        }
        if (empty($alternative) && $file->hasProperty(self::KEY_TITLE)) {
            $alternative = $file->getProperty(self::KEY_TITLE);
        }
        if (empty($alternative)) {
            $alternative = $file->getNameWithoutExtension();
        }

        return $alternative;
    }

    protected function getCaption(FileInterface $file): string
    {
        $caption = '';
        if ($file->hasProperty(self::KEY_DESCRIPTION)) {
            $caption = $file->getProperty(self::KEY_DESCRIPTION);
        }

        if (empty($caption) && $file->hasProperty(self::KEY_CAPTION)) {
            $caption = $file->getProperty(self::KEY_CAPTION);
        }

        return $caption;
    }

    protected function getCopyRightData(FileInterface $file): array
    {
        $data = [];
        $copyright = trim((string)$file->getProperty('copyright'));

        if ($copyright !== '') {
            $data = [
                self::KEY_COPYRIGHT => $copyright,
                'copyrightLabel' => '@todo: translatable copyright label',
                'buttonIconOnlyData' => [
                    'ariaLabel' => '@todo: translatable aria label',
                    'xtraClass' => 'js-copyright--toggle-off',
                    'type' => 'button',
                    'icon' => 'icon_close',
                ],
            ];
        }

        return $data;
    }
}

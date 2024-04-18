<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\LinkService;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;

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
    use MetaDataCollectorTrait;

    public const KEY_CROP_VARIANTS = 'cropVariants';
    public const KEY_HEIGHT = 'height';
    public const KEY_WIDTH = 'width';
    public const KEY_VARIANTS = 'variants';
    public const KEY_LINKED_IMAGE = 'linkedImage';
    public const KEY_ORIGINAL = 'original';
    public const KEY_OPTIONS = 'options';

    public const MEDIA_TYPE = 'image';

    public const DEFAULT_CONFIG = [
        self::KEY_CROP_VARIANTS => [
            'default' => []
        ]
    ];

    public function __construct(
        protected ContentObjectRenderer $cObj,
        protected ImageService $imageService
    ) {
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

        $labels = $this->collectLabels($config[self::MEDIA_TYPE] ??= []);
        $linkedImage = $this->collectFileReferenceLink($file, $labels);

        $imageData = [
            self::KEY_TYPE => self::MEDIA_TYPE,
            self::KEY_ORIGINAL => $file->getPublicUrl(),
            self::KEY_OPTIONS => $config[self::MEDIA_TYPE],
            self::KEY_VARIANTS => []
        ];

        if(!empty($linkedImage)) {
            $imageData[self::KEY_LINKED_IMAGE] = $linkedImage;
        }

        // collect file meta data
        ArrayUtility::mergeRecursiveWithOverrule($imageData, $this->collectMetaDataFromFile($file));

        $this->collectFileReferenceLink($file);

        foreach ($cropVariants as $variant => $variantConfig) {
            $imageData[self::KEY_VARIANTS][$variant] = $this->processCropVariant($variant, $variantConfig, $file);
        }
        return $imageData;
    }


    protected function collectFileReferenceLink(FileInterface $file, array $labels = []): array
    {
        $link = $this->createLink($file);
        if (!empty($link)) {
            $accessibility = $labels['accessibilityLinkSelf'];

            if(!empty($link['target']) && $link['target'] == '_blank') {
                $accessibility = $labels['accessibilityLinkBlank'];
            }

            if(!empty($link['title'])) {
                $accessibility = $link['title'];
            }

            $link['accessibility'] = $accessibility;
        }
        return $link;
    }

    protected function createLink(FileInterface $file): array
    {
        try {
            $link = $file->hasProperty('link') ? $file->getProperty('link') : '';

            $link = $this->cObj->createLink('', [
                'parameter' => $link
            ]);

            $link = $link->toArray();
        } catch (\Exception) {
            $link = [];
        }

        return $link;
    }

    protected function collectLabels(array $config = []): array
    {
        $labels = [];
        if (empty($config['labels'])) {
            return $labels;
        }

        foreach ($config['labels'] as $label => $ll) {
            $labels[$label] = trim($this->cObj->getData($ll));
        }

        return $labels;
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
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use Cpsit\BravoHandlebarsContent\Service\LinkService;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

#[AsTaggedItem(priority: 4)]
class ImageProcessor implements MediaProcessorInterface, ContentRendererAwareInterface
{
    use MetaDataCollectorTrait, ContentRendererTrait;

    public const KEY_CROP_VARIANTS = 'cropVariants';
    public const KEY_SRCSET = 'srcset';
    public const KEY_HEIGHT = 'height';
    public const KEY_WIDTH = 'width';
    public const KEY_VARIANTS = 'variants';
    public const KEY_LINKED_IMAGE = 'linkedImage';
    public const KEY_ORIGINAL = 'original';
    public const KEY_OPTIONS = 'options';
    public const KEY_LABELS = 'labels';

    public const MEDIA_TYPE = 'image';

    public const DEFAULT_CONFIG = [
        self::KEY_CROP_VARIANTS => [
            'default' => []
        ]
    ];

    public function __construct(
        protected ImageService $imageService,
        protected LinkService $linkService
    ) {
    }

    public function canProcess(FileInterface $file): bool
    {
        if ($file instanceof FileReference && $file->getOriginalFile()->isImage()) {
            return true;
        }

        if (method_exists($file, 'isImage') && $file->isImage()) {
            return true;
        }

        return false;
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

        #if (!empty($config[self::MEDIA_TYPE][self::KEY_SRCSET])) {
        #    $cropVariants = $config[self::MEDIA_TYPE][self::KEY_CROP_VARIANTS];
        #}

        $labels = $this->collectLabels($config[self::MEDIA_TYPE] ??= []);
        $linkedImage = $this->collectFileReferenceLink($file, $labels);

        $imageData = [
            self::KEY_TYPE => self::MEDIA_TYPE,
            self::KEY_ORIGINAL => $file->getPublicUrl(),
            self::KEY_OPTIONS => $config[self::MEDIA_TYPE],
            self::KEY_LABELS => $labels,
            self::KEY_VARIANTS => []
        ];

        if (!empty($linkedImage)) {
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
            $accessibility = $labels['accessibilityLinkSelf'] ?? '';

            if (!empty($link['target']) && $link['target'] == '_blank') {
                $accessibility = $labels['accessibilityLinkBlank'] ?? '';
            }

            if (!empty($link['title'])) {
                $accessibility = $link['title'];
            }

            $link['accessibility'] = $accessibility;
        }
        return $link;
    }

    protected function createLink(FileInterface $file): array
    {
        $link = $file->hasProperty('link') ? $file->getProperty('link') : '';
        $this->linkService->setContentObjectRenderer($this->contentObjectRenderer);
        $link = $this->linkService->resolveTypoLink($link);
        return $this->linkService->linkResultToArray($link);
    }

    protected function collectLabels(array $config = []): array
    {
        $labels = [];
        if (empty($config['labels'])) {
            return $labels;
        }

        foreach ($config['labels'] as $label => $ll) {
            $labels[$label] = trim($this->contentObjectRenderer->getData($ll));
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

        if(isset($config[self::KEY_SRCSET]) && is_array($config[self::KEY_SRCSET])) {
            $images = [];
            foreach ($config[self::KEY_SRCSET] as $key => $conf) {
                $processingInstructions = [
                    'width' => $conf['width'] ?? '',
                    'height' => $conf['height'] ?? '',
                    'minWidth' => $conf['minWidth'] ?? '',
                    'minHeight' => $conf['minHeight'] ?? '',
                    'maxWidth' => $conf['maxWidth'] ?? '',
                    'maxHeight' => $conf['maxHeight'] ?? '',
                    'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($file),
                ];
                $image = $this->imageService->applyProcessingInstructions($file, $processingInstructions);

                $images[$key] = [
                    self::KEY_SRC => $this->imageService->getImageUri($image),
                    self::KEY_WIDTH => $image->getProperty(self::KEY_WIDTH),
                    self::KEY_HEIGHT => $image->getProperty(self::KEY_HEIGHT)
                ];
            }
            return $images;
        }
        // fall back
        $config['crop'] = $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($file);
        $image = $this->imageService->applyProcessingInstructions($file, $config);
        return [
            self::KEY_SRC => $this->imageService->getImageUri($image),
            self::KEY_WIDTH => $image->getProperty(self::KEY_WIDTH),
            self::KEY_HEIGHT => $image->getProperty(self::KEY_HEIGHT)
        ];
    }
}

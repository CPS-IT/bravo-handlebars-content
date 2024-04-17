<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class VimeoProcessor implements MediaProcessorInterface
{
    use OnlineMediaProcessorTrait, MetaDataCollectorTrait;

    public const MEDIA_TYPE = 'vimeo';
    public const DEFAULT_CONFIG = [
        'width' => 0,
        'height' => 0,
        'loop' => 1,
        'api' => 0,
        'no-cookie' => 1,
        'showinfo' => 0,
        'labels' => [],
        'previewImage' => [
            'variants' => [
                'original' => [
                    'file' => [
                        'noScale' => 1,
                    ],
                ]
            ]
        ]
    ];

    public function __construct(
        protected ContentObjectRenderer $cObj,
        protected TypoScriptService $typoScriptService
    ) {

    }

    public function canProcess(FileInterface $file): bool
    {
        return ($file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo') && $this->getOnlineMediaHelper($file) !== false;
    }

    public function process(FileInterface $file, array $config = []): array
    {
        $onlineMedia = [
            'type' => self::MEDIA_TYPE
        ];
        $config = $this->getConfigOverrides($config);
        $config = $this->collectOptions($config, $file);
        $onlineMedia['publicUrl'] = $this->createVimeoUrl($config, $file);

        $previewImage = $this->getPreviewImageFromFile($file);
        if (!empty($previewImage)) {
            $onlineMedia['previewImage'] = $this->processPreviewImageVariants($previewImage, $config['previewImage']);
        }
        ArrayUtility::mergeRecursiveWithOverrule($onlineMedia, $this->collectMetaDataFromFile($file));
        $config['labels'] = $this->collectLabels($config);
        $onlineMedia['options'] = $config;
        return $onlineMedia;
    }

    protected function createVimeoUrl(array $options, FileInterface $file)
    {
        $videoIdRaw = $this->getVideoIdFromFile($file);
        $videoIdRaw = GeneralUtility::trimExplode('/', $videoIdRaw, true);

        $videoId = $videoIdRaw[0];
        $hash = $videoIdRaw[1] ?? null;

        $urlParams = [];
        if (!empty($hash)) {
            $urlParams[] = 'h=' . $hash;
        }
        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
            // If autoplay is enabled, enforce muted=1, see https://developer.chrome.com/blog/autoplay/
            $urlParams[] = 'muted=1';
        }
        if (!empty($options['loop'])) {
            $urlParams[] = 'loop=1';
        }

        if (isset($options['api']) && (int)$options['api'] === 1) {
            $urlParams[] = 'api=1';
        }
        if (!isset($options['no-cookie']) || !empty($options['no-cookie'])) {
            $urlParams[] = 'dnt=1';
        }
        $urlParams[] = 'title=' . (int)!empty($options['showinfo']);
        $urlParams[] = 'byline=' . (int)!empty($options['showinfo']);
        $urlParams[] = 'portrait=0';

        return sprintf('https://player.vimeo.com/video/%s?%s', $videoId, implode('&', $urlParams));
    }

    protected function processPreviewImageVariants(string $file, array $config = []): array
    {
        $files = [];
        foreach ($config['variants'] as $variant => $fileConfig) {
            $fileConfig = $this->typoScriptService->convertPlainArrayToTypoScriptArray($fileConfig);
            $fileConfig['file'] = $file;
            $files[$variant] = $this->cObj->cObjGetSingle('IMG_RESOURCE', $fileConfig);
        }
        return $files;
    }

    protected function getConfigOverrides(array $config): array
    {
        $configOverrides = self::DEFAULT_CONFIG;
        if (!empty($config[self::MEDIA_TYPE])) {
            ArrayUtility::mergeRecursiveWithOverrule($configOverrides, $config[self::MEDIA_TYPE]);
        }
        return $configOverrides;
    }

    protected function collectMetaDataFromFile(FileInterface $file): array
    {
        $metaData = [];
        foreach (self::META_DATA_FIELDS as $property => $key) {
            $metaData[$key] = $file->hasProperty($property) ? $file->getProperty($property) : '';
        }
        return $metaData;
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

    protected function collectOptions(array $options, FileInterface $file): array
    {
        // Check for an autoplay option at the file reference itself, if not overridden yet.
        if (!isset($options['autoplay']) && $file instanceof FileReference) {
            $autoplay = $file->getProperty('autoplay');
            if ($autoplay !== null) {
                $options['autoplay'] = $autoplay;
            }
        }
        if (!isset($options['allow'])) {
            $options['allow'] = 'fullscreen';
            if (!empty($options['autoplay'])) {
                $options['allow'] = 'autoplay; fullscreen';
            }
        }
        return $options;
    }
}

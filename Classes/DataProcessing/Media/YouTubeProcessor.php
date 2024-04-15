<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class YouTubeProcessor implements MediaProcessorInterface
{
    public const MEDIA_TYPE = 'youtube';
    public const META_DATA_FIELDS = [
        'alternative' => 'alt',
        'title' => 'title',
        'description' => 'description',
        'caption' => 'caption',
        'copyright' => 'copyright',
        'language' => 'language',
    ];

    public const DEFAULT_CONFIG = [
        'width' => 0,
        'height' => 0,
        'autoplay' => 1,
        'controls' => 1,
        'no-cookie' => 1,
        'modestbranding' => 1,
        'loop' => 1,
        'relatedVideos' => 0,
        'enablejsapi' => 0,
        'allowfullscreen' => true,
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

    /**
     * @var OnlineMediaHelperInterface|false
     */
    protected $onlineMediaHelper;

    public function __construct(
        protected ContentObjectRenderer $cObj,
        protected TypoScriptService $typoScriptService
    ) {

    }

    public function canProcess(FileInterface $file): bool
    {
        return ($file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube') && $this->getOnlineMediaHelper($file) !== false;
    }

    public function process(FileInterface $file, array $config = []): array
    {
        $onlineMedia = [];
        $config = $this->getConfigOverrides($config);
        $config = $this->collectOptions($config, $file);
        $onlineMedia['publicUrl'] = $this->createYouTubeUrl($config, $file);
        $previewImage = $this->getPreviewImageFromFile($file);
        if (!empty($previewImage)) {
            $onlineMedia['previewImage'] = $this->processPreviewImageVariants($previewImage, $config['previewImage']);
        }
        ArrayUtility::mergeRecursiveWithOverrule($onlineMedia, $this->getMetaDataFromFile($file));
        $onlineMedia['options'] = $config;
        return $onlineMedia;
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

    protected function getOriginalFile(FileInterface $file): File|FileInterface
    {
        if ($file instanceof FileReference) {
            return $file->getOriginalFile();
        }
        return $file;
    }

    protected function getMetaDataFromFile(FileInterface $file): array
    {
        $metaData = [];
        foreach (self::META_DATA_FIELDS as $property => $key) {
            $metaData[$key] = $file->hasProperty($property) ? $file->getProperty($property) : '';
        }
        return $metaData;
    }

    protected function getPreviewImageFromFile(FileInterface $file): string
    {
        $orgFile = $this->getOriginalFile($file);
        return $this->getOnlineMediaHelper($file)->getPreviewImage($orgFile);
    }

    protected function getVideoIdFromFile(FileInterface $file): string
    {
        $orgFile = $this->getOriginalFile($file);
        return $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
    }

    protected function createYouTubeUrl(array $options, FileInterface $file): string
    {
        $videoId = $this->getVideoIdFromFile($file);

        $urlParams = ['autohide=1'];
        $urlParams[] = 'controls=' . $options['controls'];
        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
            // If autoplay is enabled, enforce mute=1, see https://developer.chrome.com/blog/autoplay/
            $urlParams[] = 'mute=1';
        }
        if (!empty($options['modestbranding'])) {
            $urlParams[] = 'modestbranding=1';
        }
        if (!empty($options['loop'])) {
            $urlParams[] = 'loop=1&playlist=' . rawurlencode($videoId);
        }
        if (isset($options['relatedVideos'])) {
            $urlParams[] = 'rel=' . (int)(bool)$options['relatedVideos'];
        }
        if (!isset($options['enablejsapi']) || !empty($options['enablejsapi'])) {
            $urlParams[] = 'enablejsapi=1&origin=' . rawurlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST'));
        }

        return sprintf(
            'https://www.youtube%s.com/embed/%s?%s',
            !isset($options['no-cookie']) || !empty($options['no-cookie']) ? '-nocookie' : '',
            rawurlencode($videoId),
            implode('&', $urlParams)
        );
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

        $showPlayerControls = 1;
        $options['controls'] = (int)!empty($options['controls'] ?? $showPlayerControls);

        if (!isset($options['allow'])) {
            $options['allow'] = 'fullscreen';
            if (!empty($options['autoplay'])) {
                $options['allow'] = 'autoplay; fullscreen';
            }
        }
        return $options;
    }

    protected function getOnlineMediaHelper(FileInterface $file): bool|OnlineMediaHelperInterface
    {
        if ($this->onlineMediaHelper === null) {
            $orgFile = $file;
            if ($orgFile instanceof FileReference) {
                $orgFile = $orgFile->getOriginalFile();
            }
            if ($orgFile instanceof File) {
                $this->onlineMediaHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }
        return $this->onlineMediaHelper;
    }
}

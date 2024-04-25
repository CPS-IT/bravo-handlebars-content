<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\FileInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class AudioProcessor implements MediaProcessorInterface
{
    public const MEDIA_TYPE = 'audio';
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
        return [
            self::KEY_TYPE => self::MEDIA_TYPE,
            self::KEY_ATTRIBUTES => $this->getAttributesValue($file, $config),
            self::KEY_SRC => $file->getPublicUrl(),
            self::KEY_MIME_TYPE => $file->getMimeType()
        ];
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\FileInterface $file
     * @param array $config
     * @return string
     */
    protected function getAttributesValue(FileInterface $file, array $config): string
    {
        $attributes = [
            'autoplay' => $file->hasProperty('autoplay') ? (bool)$file->getProperty('autoplay') : false,
            'controls' => empty($config[self::MEDIA_TYPE]['controls']) || (bool)$config[self::MEDIA_TYPE]['controls'],
            'loop' => !empty($config[self::MEDIA_TYPE]['loop']) && (bool)$config[self::MEDIA_TYPE]['bool']
        ];

        $keys = [];
        foreach ($attributes as $key => $value) {
            if (!$value) {
                continue;
            }
            $keys[] = $key;
        }

        return implode(' ', $keys);
    }
}

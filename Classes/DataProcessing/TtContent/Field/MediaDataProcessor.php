<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\OnlineMedia;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class MediaDataProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        if (
            empty($variables['originalFirstMedia'])
            || !$variables['originalFirstMedia'] instanceof MediaInterface
            || !$variables['originalFirstMedia'] instanceof OnlineMedia
        ) {
            return $variables;
        }

        $media = $variables['originalFirstMedia'];
        $variables[$fieldName] = [
            'type' => $media->getProperty('type'),
            'title' => $media->getProperty('title'),
            'duration' => $media->getProperty('duration'),
            'publicUrl' => $media->getPublicUrl(),
            'onlineMediaId' => $media->getOnlineMediaId(),
            'previewImage' => $media->getPreviewImage()
        ];
        return $variables;
    }
}

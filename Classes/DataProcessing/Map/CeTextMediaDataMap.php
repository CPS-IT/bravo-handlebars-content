<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface as TtContent;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class CeTextMediaDataMap implements DataMapInterface
{
    use FieldMapTrait;

    public const DEFAULT_FIELD_MAPS = [
        TtContent::FIELD_BODYTEXT => 'textHtml',
        TtContent::FIELD_SPACE_BEFORE => 'spaceBefore',
        TtContent::FIELD_UID => 'id',
        TtContent::FIELD_IMAGE_ZOOM => 'lightbox',

        // Image mapping - start
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.src' => 'pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.image.0.variants.tablet.src' => 'pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.image.0.variants.desktop.src' => 'pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.width' => 'pictureData.imgData.width',
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.height' => 'pictureData.imgData.height',
        TtContent::FIELD_ASSETS . '.image.0.labels.accessibilityLightbox' => '@accessibility.text',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.target' => 'linkedImage.target',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.href' => 'linkedImage.url',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.accessibility' => '@accessibility.text',
        TtContent::FIELD_ASSETS . '.image.0.alternative' => 'pictureData.imgData.alt',
        TtContent::FIELD_ASSETS . '.image.0.description' => 'caption',
        TtContent::FIELD_ASSETS . '.image.0.copyright' => 'copyrightData.copyright',
        TtContent::FIELD_ASSETS . '.image.0.lightbox' => 'lightbox',
        TtContent::FIELD_ASSETS . '.image.0.lightboxCopyright' => 'lightboxCopyright',
        TtContent::FIELD_ASSETS . '.image.0.lightboxImg' => 'lightboxImg',

        // Youtube Video mapping - start
        TtContent::FIELD_ASSETS . '.youtube.0.options.labels.accessibility' => 'mediaData.@accessibility.text',
        TtContent::FIELD_ASSETS . '.youtube.0.options.labels.textHTML' => 'mediaData.textHTML',
        TtContent::FIELD_ASSETS . '.youtube.0.description' => 'mediaData.mediaCaption',
        TtContent::FIELD_ASSETS . '.youtube.0.copyrightData' => 'mediaData.copyrightData',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.mobile' => 'mediaData.pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.tablet' => 'mediaData.pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.desktop' => 'mediaData.pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.original' => 'mediaData.pictureData.imgData.src',
        TtContent::FIELD_ASSETS . '.youtube.0.options.previewImage.loading' => 'mediaData.pictureData.imgData.loading',
        TtContent::FIELD_ASSETS . '.youtube.0.options.previewImage.height' => 'mediaData.pictureData.imgData.height',
        TtContent::FIELD_ASSETS . '.youtube.0.options.previewImage.width' => 'mediaData.pictureData.imgData.width',
        TtContent::FIELD_ASSETS . '.youtube.0.publicUrl' => 'mediaData.iframeData.dataSrc',
        TtContent::FIELD_ASSETS . '.youtube.0.options.allow' => 'mediaData.iframeData.allow',
        TtContent::FIELD_ASSETS . '.youtube.0.title' => 'mediaData.iframeData.title',

        // Vimeo Video mapping - start
        TtContent::FIELD_ASSETS . '.vimeo.0.options.labels.accessibility' => 'mediaData.@accessibility.text',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.labels.textHTML' => 'mediaData.textHTML',
        TtContent::FIELD_ASSETS . '.vimeo.0.description' => 'mediaData.mediaCaption',
        TtContent::FIELD_ASSETS . '.vimeo.0.copyrightData' => 'mediaData.copyrightData',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.mobile' => 'mediaData.pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.tablet' => 'mediaData.pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.desktop' => 'mediaData.pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.original' => 'mediaData.pictureData.imgData.src',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.previewImage.loading' => 'mediaData.pictureData.imgData.loading',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.previewImage.height' => 'mediaData.pictureData.imgData.height',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.previewImage.width' => 'mediaData.pictureData.imgData.width',
        TtContent::FIELD_ASSETS . '.vimeo.0.publicUrl' => 'mediaData.iframeData.dataSrc',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.allow' => 'mediaData.iframeData.allow',
        TtContent::FIELD_ASSETS . '.vimeo.0.title' => 'mediaData.iframeData.title',

        // Audio mapping
        TtContent::FIELD_ASSETS . '.audio.0.src' => 'audioData.src',
        TtContent::FIELD_ASSETS . '.audio.0.attributes' => 'audioData.attributes',
        TtContent::FIELD_ASSETS . '.audio.0.mimeType' => 'audioData.type',
    ];

}

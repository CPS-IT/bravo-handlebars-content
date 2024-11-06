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
        TtContent::FIELD_ASSETS . '.image.0.variants.default.sourceS.src' => 'picture.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.image.0.variants.default.sourceM.src' => 'picture.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.image.0.variants.default.sourceL.src' => 'picture.sourceTextMedia.sourceL',
        'picture.sourceTextMedia.sourceS' => 'picture.img.src',
        TtContent::FIELD_ASSETS . '.image.0.variants.default.sourceS.width' => 'picture.img.width',
        TtContent::FIELD_ASSETS . '.image.0.variants.default.sourceS.height' => 'picture.img.height',
        TtContent::FIELD_ASSETS . '.image.0.labels.accessibilityLightbox' => 'accessibility',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.target' => 'linkedImage.target',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.url' => 'linkedImage.url',
        TtContent::FIELD_ASSETS . '.image.0.linkedImage.accessibility' => 'linkedImage.accessibility',
        TtContent::FIELD_ASSETS . '.image.0.alt' => 'picture.img.alt',
        TtContent::FIELD_ASSETS . '.image.0.description' => 'picture.caption',
        TtContent::FIELD_ASSETS . '.image.0.copyright' => 'picture.copyright.copyright',
        TtContent::FIELD_ASSETS . '.image.0.lightbox' => 'lightbox',
        TtContent::FIELD_ASSETS . '.image.0.lightboxCopyright' => 'lightboxCopyright',
        TtContent::FIELD_ASSETS . '.image.0.lightboxImg' => 'lightboxImg',

        // Youtube Video mapping - start
        TtContent::FIELD_ASSETS . '.youtube.0.options.labels.accessibility' => 'media.accessibility',
        TtContent::FIELD_ASSETS . '.youtube.0.options.labels.textHTML' => 'media.textHTML',
        TtContent::FIELD_ASSETS . '.youtube.0.description' => 'media.mediaCaption',
        TtContent::FIELD_ASSETS . '.youtube.0.copyright' => 'media.picture.copyright.copyright',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.mobile' => 'media.picture.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.tablet' => 'media.picture.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.desktop' => 'media.picture.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.youtube.0.previewImage.original' => 'media.picture.img.src',
        TtContent::FIELD_ASSETS . '.youtube.0.options.previewImage.height' => 'media.picture.img.height',
        TtContent::FIELD_ASSETS . '.youtube.0.options.previewImage.width' => 'media.picture.img.width',
        TtContent::FIELD_ASSETS . '.youtube.0.publicUrl' => 'media.iframeCookiebot.dataSrc',
        TtContent::FIELD_ASSETS . '.youtube.0.options.allow' => 'media.iframeData.allow',
        TtContent::FIELD_ASSETS . '.youtube.0.title' => 'media.iframeData.title',

        // Vimeo Video mapping - start
        TtContent::FIELD_ASSETS . '.vimeo.0.options.labels.accessibility' => 'media.accessibility',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.labels.textHTML' => 'media.textHTML',
        TtContent::FIELD_ASSETS . '.vimeo.0.description' => 'media.mediaCaption',
        TtContent::FIELD_ASSETS . '.vimeo.0.copyright' => 'media.picture.copyright.copyright',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.mobile' => 'media.picture.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.tablet' => 'media.picture.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.desktop' => 'media.picture.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.vimeo.0.previewImage.original' => 'media.picture.img.src',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.previewImage.height' => 'media.picture.img.height',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.previewImage.width' => 'media.picture.img.width',
        TtContent::FIELD_ASSETS . '.vimeo.0.publicUrl' => 'media.iframeCookiebot.dataSrc',
        TtContent::FIELD_ASSETS . '.vimeo.0.options.allow' => 'media.iframeData.allow',
        TtContent::FIELD_ASSETS . '.vimeo.0.title' => 'media.iframeData.title',

        // Audio mapping
        TtContent::FIELD_ASSETS . '.audio.0.src' => 'audio.src',
        TtContent::FIELD_ASSETS . '.audio.0.attributes' => 'audio.attributes',
        TtContent::FIELD_ASSETS . '.audio.0.mimeType' => 'audio.type',
    ];

}

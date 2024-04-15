<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface as TtContent;

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
class CeTextMediaDataMap implements DataMapInterface
{
    use FieldMapTrait;

    public const DEFAULT_FIELD_MAPS = [
        TtContent::FIELD_BODYTEXT => 'textHtml',
        TtContent::FIELD_HEADLINES => 'headlinesData',
        TtContent::FIELD_SPACE_BEFORE => 'spaceBefore',
        TtContent::FIELD_UID => 'id',
        TtContent::FIELD_IMAGE_ZOOM => 'lightbox',
        // note: 'assets.pictureData' must only be set for images and **not** for videos
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.src' => 'pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.image.0.variants.tablet.src' => 'pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.image.0.variants.desktop.src' => 'pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.width' => 'pictureData.imgData.width',
        TtContent::FIELD_ASSETS . '.image.0.variants.mobile.height' => 'pictureData.imgData.height',
        TtContent::FIELD_ASSETS . '.image.0.alternative' => 'pictureData.imgData.alt',
        TtContent::FIELD_ASSETS . '.image.0.caption' => 'caption',
        TtContent::FIELD_ASSETS . '.image.0.copyrightData' => 'copyrightData',
        TtContent::FIELD_ASSETS . '.image.0.copyrightData.copyright' => 'lightboxCopyright',
        // reuse previously set value for duplicate entry
        'pictureData.sourceTextMedia.sourceS' => 'pictureData.imgData.src',
        // note: 'assets.onlineMedia' must only be set for videos and **not** for images

        // Youtube Video mapping - start
        TtContent::FIELD_ASSETS . '.youtube.0.options.labels.accessibility' => 'mediaData.accessibilityData.accessibility',
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
        TtContent::FIELD_ASSETS . '.youtube.0.options.button' => 'mediaData.@icon-font--play',
        // Youtube Video mapping - end

        // audio mapping
        TtContent::FIELD_ASSETS . '.audio.0.src' => 'audioData.src',
        TtContent::FIELD_ASSETS . '.audio.0.attributes' => 'audioData.attributes',
        TtContent::FIELD_ASSETS . '.audio.0.mimeType' => 'audioData.type',
    ];

}

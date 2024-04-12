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
        TtContent::FIELD_ASSETS . '.media.0.variants.mobile.src' => 'pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.media.0.variants.tablet.src' => 'pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.media.0.variants.desktop.src' => 'pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.media.0.variants.mobile.width' => 'pictureData.imgData.width',
        TtContent::FIELD_ASSETS . '.media.0.variants.mobile.height' => 'pictureData.imgData.height',
        TtContent::FIELD_ASSETS . '.media.0.alternative' => 'pictureData.imgData.alt',
        TtContent::FIELD_ASSETS . '.media.0.caption' => 'caption',
        TtContent::FIELD_ASSETS . '.media.0.copyrightData' => 'copyrightData',
        TtContent::FIELD_ASSETS . '.media.0.copyrightData.copyright' => 'lightboxCopyright',
        // reuse previously set value for duplicate entry
        'pictureData.sourceTextMedia.sourceS' => 'pictureData.imgData.src',
        // note: 'assets.onlineMedia' must only be set for videos and **not** for images
        TtContent::FIELD_ASSETS . '.onlineMedia.publicUrl' => 'mediaData.iframeData.dataSrc',
        TtContent::FIELD_ASSETS . '.onlineMedia.previewImage' => 'mediaData.iframeData.previewImage',
        TtContent::FIELD_ASSETS . '.onlineMedia.onlineMediaId' => 'mediaData.iframeData.mediaId',
        TtContent::FIELD_ASSETS . '.onlineMedia.title' => 'mediaData.iframeData.title',
        TtContent::FIELD_ASSETS . '.onlineMedia.allow' => 'mediaData.iframeData.allow',
        TtContent::FIELD_ASSETS . '.onlineMedia.accessibilityData' => 'mediaData.accessibilityData',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@picture.sourceS' => 'mediaData.pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@picture.sourceM' => 'mediaData.pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@picture.sourceL' => 'mediaData.pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@picture.sourceXl' => 'mediaData.pictureData.sourceTextMedia.sourceXl',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@picture.imgData' => 'mediaData.pictureData.imgData',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@figure.caption' => 'mediaData.caption',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@figure.copyrightData' => 'mediaData.copyrightData',
        TtContent::FIELD_ASSETS . '.onlineMedia.pictureData.@figure.copyrightData.copyright' => 'mediaData.lightboxCopyright',
    ];

}

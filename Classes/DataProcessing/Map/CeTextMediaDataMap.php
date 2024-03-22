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
        TtContent::FIELD_ASSETS . '.@picture.sourceS' => 'pictureData.sourceTextMedia.sourceS',
        TtContent::FIELD_ASSETS . '.@picture.sourceM' => 'pictureData.sourceTextMedia.sourceM',
        TtContent::FIELD_ASSETS . '.@picture.sourceL' => 'pictureData.sourceTextMedia.sourceL',
        TtContent::FIELD_ASSETS . '.@picture.sourceXl' => 'pictureData.sourceTextMedia.sourceXl',
        TtContent::FIELD_ASSETS . '.@picture.imgData' => 'pictureData.imgData',
        TtContent::FIELD_ASSETS . '.@figure.caption' => 'caption',
        TtContent::FIELD_ASSETS . '.@figure.copyrightData' => 'copyrightData',
        TtContent::FIELD_ASSETS . '.@figure.copyrightData.copyright' => 'lightboxCopyright',
    ];

}

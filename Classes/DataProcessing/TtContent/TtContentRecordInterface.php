<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent;

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
interface TtContentRecordInterface
{
    // field names of table tt_content
    public const FIELD_ASSETS = 'assets';
    public const FIELD_BODYTEXT = 'bodytext';
    public const FIELD_FILE_COLLECTIONS = 'file_collections';
    public const FIELD_HEADER = 'header';
    public const FIELD_HEADER_LAYOUT = 'header_layout';
    public const FIELD_HEADER_LINK = 'header_link';
    public const FIELD_HEADLINES = '@headlines';
    public const FIELD_HIDDEN = 'hidden';
    public const FIELD_IMAGE_BORDER = 'imageborder';
    public const FIELD_IMAGE_COLUMNS = 'imagecols';
    public const FIELD_IMAGE_HEIGHT = 'imageheight';
    public const FIELD_IMAGE_ORIENT = 'imageorient';
    public const FIELD_IMAGE_WIDTH = 'imagewidth';
    public const FIELD_IMAGE_ZOOM = 'image_zoom';
    public const FIELD_MEDIA = 'media';
    public const FIELD_SPACE_BEFORE = 'space_before_class';
    public const FIELD_SUBHEADER = 'subheader';
    public const FIELD_UID = 'uid';
}

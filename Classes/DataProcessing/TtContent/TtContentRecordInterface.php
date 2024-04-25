<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
interface TtContentRecordInterface
{
    // field names of table tt_content
    public const FIELD_ASSETS = 'assets';
    public const FIELD_BODYTEXT = 'bodytext';
    public const FIELD_CATEGORIES = 'categories';
    public const FIELD_DATE = 'date';
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
    public const FIELD_PI_FLEXFORM = 'pi_flexform';

}

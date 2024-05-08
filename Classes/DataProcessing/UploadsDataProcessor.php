<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\Map\DataMapInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\DownloadItemsProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\FileCollectionsProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\FileReferencesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\PassThrough;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\SpaceBeforeProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\UidProcessor;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class UploadsDataProcessor extends TtContentDataProcessor implements FieldMappingInterface
{
    use FieldMappingTrait;

    public const DEFAULT_FIELDS = [
        self::FIELD_FILE_COLLECTIONS => FileCollectionsProcessor::class,
        self::FIELD_HEADER => PassThrough::class,
        self::FIELD_HEADER_LAYOUT => HeaderLayoutProcessor::class,
        self::FIELD_HEADER_LINK => HeaderLinkProcessor::class,
        self::FIELD_HEADLINES => HeadlinesProcessor::class,
        self::FIELD_HIDDEN => PassThrough::class,
        self::FIELD_MEDIA => FileReferencesProcessor::class,
        self::FIELD_SPACE_BEFORE => SpaceBeforeProcessor::class,
        self::FIELD_UID => UidProcessor::class,
        'files' => DownloadItemsProcessor::class
    ];

}



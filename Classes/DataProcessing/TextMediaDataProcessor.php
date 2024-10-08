<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\Dto\FieldProcessorConfiguration;
use Cpsit\BravoHandlebarsContent\DataProcessing\Map\DataMapInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\BodytextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ContentMediaProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ContentTextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ImageBelowTextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ImageOrientProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ImageZoomProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\MediaProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\ModifierProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\PassThrough;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\SpaceBeforeProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\UidProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\FrameClassProcessor;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class TextMediaDataProcessor extends TtContentDataProcessor implements FieldMappingInterface
{
    use FieldMappingTrait;

    public const DEFAULT_FIELDS = [
        self::FIELD_ASSETS => MediaProcessor::class,
        self::FIELD_BODYTEXT => BodytextProcessor::class,
        self::FIELD_HEADER => PassThrough::class,
        self::FIELD_HEADER_LAYOUT => HeaderLayoutProcessor::class,
        self::FIELD_HEADER_LINK => HeaderLinkProcessor::class,
        self::FIELD_HEADLINES => HeadlinesProcessor::class,
        self::FIELD_HIDDEN => PassThrough::class,
        self::FIELD_IMAGE_BORDER => PassThrough::class,
        self::FIELD_IMAGE_COLUMNS => PassThrough::class, //todo
        self::FIELD_IMAGE_HEIGHT => PassThrough::class, //todo
        self::FIELD_IMAGE_ORIENT => ImageOrientProcessor::class,
        self::FIELD_IMAGE_WIDTH => PassThrough::class, //todo
        self::FIELD_IMAGE_ZOOM => ImageZoomProcessor::class,
        self::FIELD_SPACE_BEFORE => SpaceBeforeProcessor::class,
        self::FIELD_UID => UidProcessor::class,
        self::FIELD_FRAME_CLASS => FrameClassProcessor::class,
        'modifier' => ModifierProcessor::class,
        'imageBelowText' => ImageBelowTextProcessor::class,
        'contentText' => ContentTextProcessor::class,
        'contentMedia' => ContentMediaProcessor::class,
    ];


}



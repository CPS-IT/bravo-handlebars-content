<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\Map\DataMapInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\BodytextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
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

class TextDataProcessor extends TtContentDataProcessor implements FieldMappingInterface
{
    use FieldMappingTrait;
}



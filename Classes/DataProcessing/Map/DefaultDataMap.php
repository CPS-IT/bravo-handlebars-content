<?php

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface as TtContent;
use SplObjectStorage;

class DefaultDataMap implements DataMapInterface
{
    use FieldMapTrait;
    public const DEFAULT_FIELD_MAPS = [
        TtContent::FIELD_UID => 'id',
    ];
}

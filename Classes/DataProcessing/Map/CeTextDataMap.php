<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface as TtContent;
use SplObjectStorage;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class CeTextDataMap implements DataMapInterface
{
    public const DEFAULT_FIELD_MAPS = [
        TtContent::FIELD_BODYTEXT => 'textHtml',
        TtContent::FIELD_SPACE_BEFORE => 'spaceBefore',
        TtContent::FIELD_UID => 'id',
    ];

    protected SplObjectStorage $fieldMaps;

    public function __construct() {
        $this->fieldMaps = new SplObjectStorage();
        foreach (self::DEFAULT_FIELD_MAPS as $source => $target) {
            $this->fieldMaps->attach(new FieldMap($source, $target));
        }
    }

    /**
     * @return SplObjectStorage<FieldMap>
     */
    public function getFieldMaps(): SplObjectStorage
    {
        return $this->fieldMaps;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
readonly class FieldMap
{
    public function __construct(
        public string $sourcePath,
        public string $targetPath,
        public string $delimiter = '.'
    )
    {}

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function getTargetPath(): string
    {
        return $this->targetPath;
    }
}

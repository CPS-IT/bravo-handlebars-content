<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use TYPO3\CMS\Core\Resource\FileInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
interface MediaProcessorInterface
{
    public function canProcess(FileInterface $file): bool;

    public function process(FileInterface $file, array $config = []): array;
}

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
    public const KEY_ATTRIBUTES = 'attributes';
    public const KEY_CAPTION = 'caption';
    public const KEY_COPYRIGHT = 'copyright';
    public const KEY_MIME_TYPE = 'mimeType';
    public const KEY_SRC = 'src';
    public const KEY_TYPE = 'type';


    public function canProcess(FileInterface $file): bool;

    public function process(FileInterface $file, array $config = []): array;
}

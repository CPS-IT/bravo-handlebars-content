<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use TYPO3\CMS\Core\Resource\FileInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
#[AsTaggedItem(priority: -99)]
class NullProcessor implements MediaProcessorInterface
{

    public function canProcess(FileInterface $file): bool
    {
        return true;
    }

    public function process(FileInterface $file, array $config = []): array
    {
        return [];
    }
}

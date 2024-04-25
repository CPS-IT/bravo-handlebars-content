<?php

namespace Cpsit\BravoHandlebarsContent\Service;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use TYPO3\CMS\Core\Resource\FileInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class MediaDataService implements ContentRendererAwareInterface
{
    use ContentRendererTrait;

    /** @var array<MediaProcessorInterface> */
    protected array $processorInstances = [];

    public function __construct(iterable $processorInstances)
    {
        $this->processorInstances = iterator_to_array($processorInstances);
    }

    /**
     * Processes a file according to its type.
     *
     * @param FileInterface $file
     * @param array $config Optional configuration like width, height or additional attributes
     * @return array Data for template
     */
    public function process(FileInterface $file, array $config = []): array
    {
        return $this->getProcessor($file)->process($file, $config);
    }

    protected function getProcessor(FileInterface $file): MediaProcessorInterface
    {
        foreach ($this->processorInstances as $processorInstance) {
            if (!$processorInstance->canProcess($file)) {
                continue;
            }
            if($processorInstance instanceof ContentRendererAwareInterface) {
                $processorInstance->setContentObjectRenderer($this->contentObjectRenderer);
            }
            return $processorInstance;
        }
    }
}

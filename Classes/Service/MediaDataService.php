<?php

namespace Cpsit\BravoHandlebarsContent\Service;

use Cpsit\BravoHandlebarsContent\DataProcessing\Media\AudioProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\ImageProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\MediaProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\NullProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\VimeoProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\Media\YouTubeProcessor;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class MediaDataService
{
    protected array $classNames = [
        ImageProcessor::class,
        AudioProcessor::class,
        YouTubeProcessor::class,
        VimeoProcessor::class,
        // note: NullProcessor must be the last one
        NullProcessor::class
    ];

    /** @var array<MediaProcessorInterface> */
    protected array $processorInstances = [];

    public function __construct()
    {
        foreach ($this->classNames as $className) {
            $this->processorInstances[] = GeneralUtility::makeInstance($className);
        }
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
            return $processorInstance;
        }
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
interface FieldAwareProcessorInterface
{
    /**
     * @throws InvalidClassException
     */
    public function instantiateFieldProcessor(
        string                $processorClass,
        ContentObjectRenderer $contentObjectRenderer,
    ): FieldProcessorInterface;

    /**
     * @param array<string> $requiredKeys An array of key required for processing
     * @throws InvalidClassException
     */
    public function processFields(ContentObjectRenderer $cObj, array $processProcessedData, array $config = []): array;
}

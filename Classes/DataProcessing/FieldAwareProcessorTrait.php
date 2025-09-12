<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
trait FieldAwareProcessorTrait
{
    public const MESSAGE_INVALID_FIELD_PROCESSOR = 'FieldProcessor %s configured in class %s must implement interface %s.';

    public const CODE_INVALID_FIELD_PROCESSOR = 1709319555;

    protected array $fieldMap = [];

    public function instantiateFieldProcessor(
        string $processorClass,
        ContentObjectRenderer $contentObjectRenderer,
        array $settings = []
    ): FieldProcessorInterface {
        $this->assertValidFieldProcessorClass($processorClass);
        /** @var FieldProcessorInterface $processor */
        $processor = GeneralUtility::makeInstance($processorClass);

        if ($processor instanceof ContentRendererAwareInterface) {
            $processor->setContentObjectRenderer($contentObjectRenderer);
        }

        return $processor;
    }

    public function processFields(ContentObjectRenderer $cObj, array $processedData, array $settings = []): array
    {
        $data = $processedData['data'];

        $processedData = $this->processDefaultFields($cObj, $data, $processedData, $settings);

        return array_merge(
            $processedData,
            $this->processCustomFields($cObj, $data, $processedData)
        );
    }

    /**
     * @throws InvalidClassException
     */
    protected function assertValidFieldProcessorClass(string $processorClass): void
    {
        if (!in_array(FieldProcessorInterface::class, class_implements($processorClass), true)) {
            $message = sprintf(
                TtContentDataProcessor::MESSAGE_INVALID_FIELD_PROCESSOR,
                $processorClass,
                static::class,
                FieldProcessorInterface::class
            );

            throw new InvalidClassException($message, TtContentDataProcessor::CODE_INVALID_FIELD_PROCESSOR);
        }
    }

    /**
     * @param ContentObjectRenderer $cObj
     * @param array $processedData
     * @param mixed $data
     *
     * @throws InvalidClassException
     *
     * @return array|mixed
     */
    protected function processDefaultFields(ContentObjectRenderer $cObj, $data, array $processedData, array $settings): mixed
    {
        $variables = [];
        if (empty($this->fieldMap) && defined('static::DEFAULT_FIELDS')) {
            $this->fieldMap = static::DEFAULT_FIELDS;
        }

        foreach ($this->fieldMap as $fieldName => $processorClass) {
            if (empty($processorClass) || !in_array($fieldName, $this->requiredKeys, true)) {
                continue;
            }

            $processor = $this->instantiateFieldProcessor($processorClass, $cObj, $settings);
            $variables = $processor->process($fieldName, $data, $processedData);
            $processedData = array_merge($processedData, $variables);
        }

        return $processedData;
    }

    /**
     * Override this method in order to process custom fields
     */
    protected function processCustomFields(
        ContentObjectRenderer $contentObjectRenderer,
        array $data,
        array $processedData,
    ): array {
        return $processedData;
    }
}

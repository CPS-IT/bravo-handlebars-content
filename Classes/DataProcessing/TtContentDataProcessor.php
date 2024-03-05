<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\BodytextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\PassThrough;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\SpaceBeforeProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\UidProcessor;
use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class TtContentDataProcessor implements DataProcessorInterface
{
    use ProcessorVariablesTrait;

    // keys for configuration
    public const KEY_FIELDS = 'fields';

    // field names of table tt_content
    public const FIELD_BODYTEXT = 'bodytext';
    public const FIELD_HEADER = 'header';
    public const FIELD_HEADER_LAYOUT = 'header_layout';
    public const FIELD_HEADER_LINK = 'header_link';
    public const FIELD_HIDDEN = 'hidden';
    public const FIELD_UID = 'uid';
    public const FIELD_SPACE_BEFORE = 'space_before_class';

    public const DEFAULT_FIELDS = [
        self::FIELD_UID => UidProcessor::class,
        self::FIELD_SPACE_BEFORE => SpaceBeforeProcessor::class,
        self::FIELD_BODYTEXT => BodytextProcessor::class,
        self::FIELD_HEADER => PassThrough::class,
        self::FIELD_HEADER_LAYOUT => HeaderLayoutProcessor::class,
        self::FIELD_HEADER_LINK => HeaderLinkProcessor::class,
        '@headlines' => HeadlinesProcessor::class,
        self::FIELD_HIDDEN => PassThrough::class
    ];

    public const MESSAGE_INVALID_FIELD_PROCESSOR = 'FieldProcessor %s configured in class %s must implement interface %s.';
    public const CODE_INVALID_FIELD_PROCESSOR = 1709319555;

    /**
     * @inheritDoc
     * @throws InvalidClassException
     */
    public function process(
        ContentObjectRenderer $cObj,
        array                 $contentObjectConfiguration,
        array                 $processorConfiguration,
        array                 $processedData
    ): array
    {
        $this->readSettingsFromConfig($processorConfiguration);

        $variables = [];
        $requiredKeys = array_keys(self::DEFAULT_FIELDS);
        if(!empty($this->settings[self::KEY_FIELDS])) {
            $requiredKeys = GeneralUtility::trimExplode(',', $this->settings[self::KEY_FIELDS]);
        }
        // todo: process selection of fields only when 'fields' is set
        foreach (self::DEFAULT_FIELDS as $fieldName => $processorClass) {
            if (empty($processorClass) || !in_array($fieldName, $requiredKeys, true)) {
                continue;
            }
            $processor = $this->instantiateFieldProcessor($processorClass, $cObj);
            $variables = $processor->process($fieldName, $processedData['data'], $variables);
        }

        return $variables;
    }

    /**
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidClassException
     */
    protected function assertValidFieldProcessorClass(string $processorClass): void
    {
        if (!in_array(FieldProcessorInterface::class, class_implements($processorClass), true)) {
            $message = sprintf(
                self::MESSAGE_INVALID_FIELD_PROCESSOR,
                $processorClass,
                get_class($this),
                FieldProcessorInterface::class
            );
            throw new InvalidClassException($message, self::CODE_INVALID_FIELD_PROCESSOR);
        }
    }

    /**
     * @param string $processorClass
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer
     * @return \Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidClassException
     */
    protected function instantiateFieldProcessor(
        string                $processorClass,
        ContentObjectRenderer $contentObjectRenderer
    ): FieldProcessorInterface
    {
        $this->assertValidFieldProcessorClass($processorClass);
        /** @var  $processor FieldProcessorInterface */
        $processor = GeneralUtility::makeInstance($processorClass, $contentObjectRenderer);
        return $processor;
    }
}



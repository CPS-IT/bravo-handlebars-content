<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\BodytextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\PassThrough;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\SpaceBeforeProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\UidProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
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

class TtContentDataProcessor implements DataProcessorInterface, FieldAwareProcessorInterface, TtContentRecordInterface
{
    use FieldAwareProcessorTrait,
        ProcessorVariablesTrait;

    // keys for configuration
    public const KEY_FIELDS = 'fields';


    public const DEFAULT_FIELDS = [
        self::FIELD_BODYTEXT => BodytextProcessor::class,
        self::FIELD_HEADER => PassThrough::class,
        self::FIELD_HEADER_LAYOUT => HeaderLayoutProcessor::class,
        // note: `header_link` must be processed before `@headlines`
        self::FIELD_HEADER_LINK => HeaderLinkProcessor::class,
        self::FIELD_HEADLINES => HeadlinesProcessor::class,
        self::FIELD_HIDDEN => PassThrough::class,
        self::FIELD_SPACE_BEFORE => SpaceBeforeProcessor::class,
        self::FIELD_UID => UidProcessor::class,
    ];


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

        $requiredKeys = array_keys(static::DEFAULT_FIELDS);
        if(!empty($this->settings[self::KEY_FIELDS])) {
            $requiredKeys = GeneralUtility::trimExplode(',', $this->settings[self::KEY_FIELDS]);
        }
        $variables = $this->processFields($requiredKeys, $cObj, $processedData['data']);

        if($this instanceof FieldMappingInterface) {
            $variables = $this->map($variables);
        }
        return array_merge($processedData, $variables);
    }
}



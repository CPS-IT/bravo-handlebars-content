<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\DataProcessing\Dto\FieldProcessorConfiguration;
use Cpsit\BravoHandlebarsContent\DataProcessing\Map\DataMapInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\BodytextProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\FrameClassProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLayoutProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeaderLinkProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\HeadlinesProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\PassThrough;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\SpaceBeforeProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field\UidProcessor;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;
use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;

class TtContentDataProcessor implements DataProcessorInterface, FieldAwareProcessorInterface, TtContentRecordInterface
{
    use FieldAwareProcessorTrait,
        ProcessorVariablesTrait,
        LocalizationTrait;


    public const DEFAULT_FIELDS = [
        self::FIELD_BODYTEXT => BodytextProcessor::class,
        self::FIELD_HEADER => PassThrough::class,
        self::FIELD_HEADER_LAYOUT => HeaderLayoutProcessor::class,
        // note: `header_link` must be processed before `headlines`
        self::FIELD_HEADER_LINK => HeaderLinkProcessor::class,
        self::FIELD_HEADLINES => HeadlinesProcessor::class,
        self::FIELD_HIDDEN => PassThrough::class,
        self::FIELD_SPACE_BEFORE => SpaceBeforeProcessor::class,
        self::FIELD_UID => UidProcessor::class,
        self::FIELD_FRAME_CLASS => FrameClassProcessor::class,
    ];

    public function __construct(
        protected FieldProcessorConfiguration $fieldProcessorConfiguration,
        protected DataMapInterface $dataMap,
        private readonly ContentObjectRenderer  $contentObjectRenderer,
        private readonly LanguageServiceFactory $languageServiceFactory

    ) {
    }

    protected array $requiredKeys = [];
    protected array $processorConfiguration = [];

    /**
     * @inheritDoc
     * @throws InvalidClassException
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $this->processorConfiguration = $processorConfiguration;

        $this->readSettingsFromConfig($this->processorConfiguration);

        if (!empty($this->settings['fieldConfig'])) {
            $this->fieldProcessorConfiguration->set($this->settings['fieldConfig']);
        }

        $variables = $this->processFields($cObj, $processedData, $this->settings);
        $variables = $this->processLocalLang($contentObjectConfiguration, $variables);

        if ($this instanceof FieldMappingInterface) {
            $variables = $this->map($variables);
        }
        return array_merge($processedData, $variables);
    }

    /**
     * @param array $contentObjectConfiguration
     * @param mixed $variables
     * @return mixed
     */
    protected function processLocalLang(array $contentObjectConfiguration, array $variables): mixed
    {
        if (!empty($contentObjectConfiguration['localLang.'])) {
            $localizedStrings = [];
            try {
                $localLangConfig = $this->getTypoScriptToPlainArray($contentObjectConfiguration['localLang.']);
                $localizedStrings = $this->getLocalizedStrings($localLangConfig);
            } catch (InvalidConfigurationException|ContentRenderingException $e) {
            }
            $as = $localLangConfig['as'] ?? 'localLang';
            $variables[$as] = $localizedStrings;
        }
        return $variables;
    }
}



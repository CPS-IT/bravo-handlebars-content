<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;
use TYPO3\CMS\Frontend\DataProcessing\DataProcessorRegistry;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class CollectionDataProcessor implements DataProcessorInterface
{
    public const RESERVED_VARIABLE_NAMES = [
        'data', 'current'
    ];

    public function __construct(
        private ContentObjectRenderer          $contentObjectRenderer,
        private readonly ContainerInterface    $container,
        private readonly DataProcessorRegistry $dataProcessorRegistry

    )
    {
    }

    /**
     * Returns $processedData enriched by variables defined in
     * $processorConfiguration. Variables may contain a string if they define
     * a content object or (nested) data if they define a dataProcessor
     */
    public function process(
        ContentObjectRenderer $cObj,
        array                 $contentObjectConfiguration,
        array                 $processorConfiguration,
        array                 $processedData): array
    {
        // this content object renderer is already initialized with request, context etc.
        $this->contentObjectRenderer = $cObj;

        if (
            isset($processorConfiguration['if.'])
            && !$this->contentObjectRenderer->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $targetVariableName = $this->contentObjectRenderer->stdWrapValue(
            'as',
            $processorConfiguration
        );
        if (empty($targetVariableName)) {
            throw new InvalidConfigurationException(
                sprintf('Missing configuration "as" in %s', get_class($this)),
                1713766921
            );
        }
        $variables = [];
        $variablesToProcess = (array)($processorConfiguration['variables.'] ?? []);
        foreach ($variablesToProcess as $variableName => $objectType) {
            if (is_array($objectType)) {
                continue;
            }
            $this->assertValidVariableName($variableName);
            $configuration = $variablesToProcess[$variableName . '.'] ?? [];
            $as = !empty($configuration['as']) && is_string($configuration['as']) ? $configuration['as'] : $variableName;
            if ($this->isContentObject($objectType)) {
                $variables[$as] = $cObj->cObjGetSingle(
                    $objectType, $configuration, 'variables.' . $variableName
                );
            }
            if ($this->isDataProcessor($objectType)) {
                // note: we must NOT pass $processedData here, otherwise we would
                // get infinitely nested results
                $localProcessed = $this->getDataProcessor($objectType)
                    ->process($this->contentObjectRenderer,
                        $processorConfiguration,
                        $configuration,
                        []
                    );
                $variables[$as] = $localProcessed[$as];
            }
        }

        $processedData[$targetVariableName] = $variables;
        return $processedData;
    }

    /**
     * @param mixed $variableName
     * @return void
     */
    protected function assertValidVariableName(mixed $variableName): void
    {
        if (in_array($variableName, self::RESERVED_VARIABLE_NAMES, true)) {
            $message =
                sprintf('Invalid variable name %s. This name is reserved',
                    $variableName
                );
            throw new \InvalidArgumentException($message, 1713637292);
        }
    }

    protected function isContentObject(string $objectType): bool
    {
        try {
            $contentObject = $this->contentObjectRenderer->getContentObject($objectType);
        } catch (ContentRenderingException $e) {
            return false;
        }

        return ($contentObject instanceof AbstractContentObject);
    }

    protected function isDataProcessor(string $objectType): bool
    {
        $dataProcessor = $this->dataProcessorRegistry->getDataProcessor($objectType);
        if (
            $dataProcessor instanceof DataProcessorInterface
            ||
            ($this->container->has($objectType)
                && ($this->container->get($objectType) instanceof DataProcessorInterface))
        ) {
            return true;
        }

        return (
            class_exists($objectType)
            && in_array(DataProcessorInterface::class, class_implements($objectType), true));
    }

    protected function getDataProcessor(string $objectType): DataProcessorInterface
    {
        $processor = $this->dataProcessorRegistry->getDataProcessor($objectType);
        if (
            null === $processor
            && $this->container->has($objectType)) {
            try {
                $processor = $this->container->get($objectType);
            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }

        if (
            !$processor instanceof DataProcessorInterface
            && class_exists($objectType)
            && in_array(DataProcessorInterface::class, class_implements($objectType), true)
        ) {
            $processor = $this->dataProcessorRegistry->getDataProcessor($objectType);
        }

        return $processor ?? GeneralUtility::makeInstance(NullDataProcessor::class);
    }
}

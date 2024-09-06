<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidConfigurationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
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

/**
 * Resolved nested processors
 *
 *  Example:
 *  lib.serial = handlebarsSerial
 *  lib.serial {
 *      // Optional: if not present results will be merged in $processedData
 *      as = targetVariableName
 *      dataProcessing {
 *          processor = processorIdentifier
 *          processor {
 *              //... configuration
 *          }
 *
 *          otherProcessor = processorIdentifier
 *          otherProcessor {
 *              //... configuration
 *          }
 *      }
 *  }
 */
class SerialDataProcessor implements DataProcessorInterface
{
    protected ?ContentObjectRenderer $contentObjectRenderer = null;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly DataProcessorRegistry $dataProcessorRegistry,
    ) {}

    /**
     * Returns $processedData enriched by variables defined in
     * $processorConfiguration. Variables may contain a string if they define
     * a content object or (nested) data if they define a dataProcessor
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {

        if (
            isset($processorConfiguration['if.'])
            && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, '');
        $data = [];

        if (
            !empty($processorConfiguration['dataProcessing.'])
            && is_array($processorConfiguration['dataProcessing.'])
        ) {
            $processors = $processorConfiguration['dataProcessing.'];
            $processorKeys = array_filter(
                array_keys($processors),
                fn($n) => !str_ends_with($n, '.')
            );
            foreach ($processorKeys as $key) {
                $dataProcessor = $this->dataProcessorRegistry->getDataProcessor($processors[$key])
                    ?? $this->getDataProcessor($processors[$key]);
                if ($dataProcessor === null) {
                    continue;
                }
                $configuration = $processors[$key . '.'] ?? [];
                $data = $dataProcessor->process(
                    $cObj,
                    $processorConfiguration,
                    $configuration,
                    $data
                );
            }
        }

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $data;
        } else {
            ArrayUtility::mergeRecursiveWithOverrule($processedData, $data);
        }
        return $processedData;
    }

    private function getDataProcessor(string $serviceName): DataProcessorInterface
    {
        if (!$this->container->has($serviceName)) {
            // assume serviceName is the class name if it is not available in the container
            return $this->instantiateDataProcessor($serviceName);
        }

        $dataProcessor = $this->container->get($serviceName);
        if (!$dataProcessor instanceof DataProcessorInterface) {
            throw new \UnexpectedValueException(
                'Processor with service name "' . $serviceName . '" ' .
                'must implement interface "' . DataProcessorInterface::class . '"',
                1635927108
            );
        }
        return $dataProcessor;
    }

    private function instantiateDataProcessor(string $className): DataProcessorInterface
    {
        if (!class_exists($className)) {
            throw new \UnexpectedValueException('Processor class or service name "' . $className . '" does not exist!', 1427455378);
        }

        if (!in_array(DataProcessorInterface::class, class_implements($className) ?: [], true)) {
            throw new \UnexpectedValueException(
                'Processor with class name "' . $className . '" ' .
                'must implement interface "' . DataProcessorInterface::class . '"',
                1427455377
            );
        }
        return GeneralUtility::makeInstance($className);
    }


}

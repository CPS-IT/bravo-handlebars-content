<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Exception\InvalidClassException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
trait FieldAwareProcessorTrait
{
    public const MESSAGE_INVALID_FIELD_PROCESSOR = 'FieldProcessor %s configured in class %s must implement interface %s.';
    public const CODE_INVALID_FIELD_PROCESSOR = 1709319555;

    protected array $fieldMap = [];

    public function instantiateFieldProcessor(
        string                $processorClass,
        ContentObjectRenderer $contentObjectRenderer
    ): FieldProcessorInterface
    {
        $this->assertValidFieldProcessorClass($processorClass);
        /** @var  $processor FieldProcessorInterface */
        $processor = GeneralUtility::makeInstance($processorClass);
        return $processor;
    }

    /**
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidClassException
     */
    protected function assertValidFieldProcessorClass(string $processorClass): void
    {
        if (!in_array(FieldProcessorInterface::class, class_implements($processorClass), true)) {
            $message = sprintf(
                TtContentDataProcessor::MESSAGE_INVALID_FIELD_PROCESSOR,
                $processorClass,
                get_class($this),
                FieldProcessorInterface::class
            );
            throw new InvalidClassException($message, TtContentDataProcessor::CODE_INVALID_FIELD_PROCESSOR);
        }
    }

    public function processFields(array $requiredKeys, ContentObjectRenderer $cObj, array $data): array
    {
        $variables = $this->processDefaultFields($requiredKeys, $cObj, $data);
        return $this->processCustomFields($cObj, $data, $variables);
    }

    /**
     * @param array $requiredKeys
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj
     * @param array $data
     * @return array|mixed
     * @throws \Cpsit\BravoHandlebarsContent\Exception\InvalidClassException
     */
    protected function processDefaultFields(array $requiredKeys, ContentObjectRenderer $cObj, array $data): mixed
    {
        $variables = [];
        if (empty($this->fieldMap) && defined('static::DEFAULT_FIELDS')) {
            $this->fieldMap = static::DEFAULT_FIELDS;
        }

        foreach ($this->fieldMap as $fieldName => $processorClass) {
            if (empty($processorClass) || !in_array($fieldName, $requiredKeys, true)) {
                continue;
            }
            $processor = $this->instantiateFieldProcessor($processorClass, $cObj);
            $variables = $processor->process($fieldName, $data, $variables);
        }
        return $variables;
    }

    /**
     * Override this method in order to process custom fields
     */
    protected function processCustomFields(
        ContentObjectRenderer $contentObjectRenderer,
        array                 $data,
        array                 $variables
    ): array
    {
        return $variables;
    }
}

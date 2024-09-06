<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class FlexformProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;
    public const FIELD_NAME = 'pi_flexform';

    public function __construct(
        protected FlexFormService $flexFormService,
    )
    {

    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        $value = $data[self::FIELD_NAME];
        $variables[$fieldName] = $this->flexFormService
            ->convertFlexFormContentToArray($value);

        return $variables;
    }
}

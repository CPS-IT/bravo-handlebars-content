<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;


use TYPO3\CMS\Core\Utility\ArrayUtility;
use Cpsit\BravoHandlebarsContent\DataProcessing\Map\FieldMap;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
trait FieldMappingTrait
{
    public function map(array $variables): array {
        /** @var FieldMap $fieldMap */
        foreach ($this->dataMap->getFieldMaps() as $fieldMap) {
            if(ArrayUtility::isValidPath($variables, $fieldMap->sourcePath, $fieldMap->delimiter)) {
                $value = ArrayUtility::getValueByPath(
                    $variables,
                    $fieldMap->sourcePath,
                    $fieldMap->delimiter
                );
                $variables = ArrayUtility::setValueByPath(
                    $variables,
                    $fieldMap->targetPath,
                    $value,
                    $fieldMap->delimiter
                );
            }
        }

        return $variables;
    }

}

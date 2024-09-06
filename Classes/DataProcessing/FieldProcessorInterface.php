<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
interface FieldProcessorInterface
{
    /**
     * @param string $fieldName Field to processes
     * @param array $data  Raw data (record)
     * @param array $variables Already processed variables. Will be returned by parent data processor.
     * @return array
     */
    public function process(string $fieldName, array $data, array $variables): array;

}

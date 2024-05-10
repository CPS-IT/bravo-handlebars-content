<?php
/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;


/**
 * Fetch records from the database, using the default .select syntax from TypoScript.
 *
 * Example TypoScript configuration:
 *
 * 10 = handlebarsDatabaseQuery
 * 10 {
 *   table = tt_address
 *   pidInList = 123
 *   where = company="Acme" AND first_name="Ralph"
 *   orderBy = sorting DESC
 *   as = addresses
 *   removeDataKey = 1
 *   dataProcessing {
 *     10 = files
 *     10 {
 *       references.fieldName = image
 *     }
 *   }
 * }
 *
 * where "as" means the variable to be containing the result-set from the DB query.
 */
class DatabaseQueryProcessor extends \TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $processedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);
        $removeDataKey = $cObj->stdWrapValue('removeDataKey', $processorConfiguration, 0);

        if ($removeDataKey) {
            $records = [];
            $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'records');
            foreach ($processedData[$targetVariableName] as $record) {
                $records[] = $record['data'];
            }

            $processedData[$targetVariableName] = $records;
        }
        return $processedData;
    }
}

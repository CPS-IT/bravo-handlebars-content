<?php
/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
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

        // note: we work with a clone of $cObj here in order to prevent side effects of changing its data array
        $clone = clone $cObj;
        if(!empty($processorConfiguration['removePrefixes']) && !empty ($processorConfiguration['prefixedFields'])) {
            // remove table prefixes from group fields
            $prefixes = GeneralUtility::trimExplode(',', $processorConfiguration['removePrefixes'], true);
            $prefixedFields = GeneralUtility::trimExplode(',', $processorConfiguration['prefixedFields'], true);
            foreach ($prefixes as $prefix) {
                foreach ($prefixedFields as $prefixedField) {
                    if(empty($processedData['data'][$prefixedField])) {
                        continue;
                    };
                    $processedData['data'][$prefixedField] = str_replace($prefix, '', $processedData['data'][$prefixedField]);
                }
            }

            $clone->data = $processedData['data'];
        }

        $processedData = parent::process($clone, $contentObjectConfiguration, $processorConfiguration, $processedData);
        $removeDataKey = $cObj->stdWrapValue('removeDataKey', $processorConfiguration, 0);
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'records');
        $data = $processedData[$targetVariableName] ?? [];

        if ($removeDataKey && !empty($data) && is_array($data)) {
            $records = [];
            foreach ($data as $processedRecord) {
                $record = $processedRecord['data'];
                foreach ($processedRecord as $key => $value) {
                    if ($key !== 'data') {
                        $record[$key] = $value;
                    }
                }
                $records[] = $record;
            }
            $processedData[$targetVariableName] = $records;
        }
        return $processedData;
    }
}

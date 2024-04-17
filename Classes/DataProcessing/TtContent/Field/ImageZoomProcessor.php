<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ImageZoomProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function process(string $fieldName, array $data, array $variables): array
    {
        $stop = 1;
        if(
            empty($data[TtContentRecordInterface::FIELD_IMAGE_ZOOM])
            || !(bool)$data[TtContentRecordInterface::FIELD_IMAGE_ZOOM]
            || empty($variables[TtContentRecordInterface::FIELD_ASSETS]['image'])
        ) {
            // nothing to do
            return $variables;
        }

        foreach ($variables[TtContentRecordInterface::FIELD_ASSETS]['image'] as $key => $image) {
            if(!empty($image['linkedImage']['href'])){
                continue;
            }
            $variables[TtContentRecordInterface::FIELD_ASSETS]['image'][$key]['lightbox'] = 1;
            $variables[TtContentRecordInterface::FIELD_ASSETS]['image'][$key]['lightboxCopyright'] =
                $variables[TtContentRecordInterface::FIELD_ASSETS]['image'][$key]['copyright'];
            $variables[TtContentRecordInterface::FIELD_ASSETS]['image'][$key]['lightboxImg'] =
                $variables[TtContentRecordInterface::FIELD_ASSETS]['image'][$key]['original'];
        }

        return $variables;
    }
}

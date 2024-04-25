<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\LinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class LinkProcessor implements FieldProcessorInterface, ContentRendererAwareInterface
{
    use FieldProcessorConfigTrait, ContentRendererTrait;

    public function __construct(protected LinkService $linkService)
    {
    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        if (empty($data[$fieldName])) {
            return $variables;
        }
        $this->linkService->setContentObjectRenderer($this->cObj);
        $link = $this->linkService->resolveTypoLink($data[$fieldName]);
        $variables[$fieldName] = $this->linkService->linkResultToArray($link);
        return $variables;
    }
}

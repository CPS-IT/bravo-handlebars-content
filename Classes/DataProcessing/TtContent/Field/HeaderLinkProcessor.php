<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\LinkService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class HeaderLinkProcessor implements FieldProcessorInterface, ContentRendererAwareInterface
{
    use FieldProcessorConfigTrait, ContentRendererTrait;


    public function __construct(protected LinkService $linkService)
    {
    }

    public function process(string $fieldName, array $data, array $variables): array
    {
        $this->linkService->setContentObjectRenderer($this->cObj);

        $typoLink = $data['header_link'] ?? '';
        $link = $this->linkService->resolveTypoLink($typoLink);
        $variables[$fieldName] = $this->linkService->linkResultToArray($link);
        return $variables;
    }
}

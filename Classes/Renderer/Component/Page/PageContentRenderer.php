<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handelbar page package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\DenaSitepackage\Renderer\Component\Page;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Page;
use Cpsit\Typo3HandlebarsComponents\Renderer\Component\Page\PageContentRendererInterface;
use Fr\Typo3Handlebars\ContentObjectRendererAwareInterface;
use Fr\Typo3Handlebars\Traits\ContentObjectRendererAwareTrait;

class PageContentRenderer implements PageContentRendererInterface, ContentObjectRendererAwareInterface
{
    use ContentObjectRendererAwareTrait;

    /**
     * Render page content for given page.
     *
     * @inheritdoc
     */
    public function render(Page $page, array $configuration): string
    {
        $this->assertContentObjectRendererIsAvailable();

        if (!isset($configuration['userFunc.'])) {
            return '';
        }

        return $this->contentObjectRenderer->cObjGetSingle(
            $configuration['userFunc.']['content'],
            $configuration['userFunc.']['content.'],
        );
    }
}

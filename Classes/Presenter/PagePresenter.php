<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Presenter;

use Cpsit\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Cpsit\Typo3HandlebarsComponents\Presenter\AbstractPagePresenter;

/**
 * TYPO3 handlebars page presenter
 * See: https://github.com/CPS-IT/handlebars-components/blob/main/Documentation/Components/PageRendering.md#custom-pagepresenter
 */
class PagePresenter extends AbstractPagePresenter
{

    protected function determineTemplateName(PageProviderResponse $data): string
    {
        return '@1col';
    }
}

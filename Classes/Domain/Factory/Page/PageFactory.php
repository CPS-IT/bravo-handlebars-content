<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handelbar page package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
namespace Cpsit\BravoHandelbarPage\Domain\Factory\Page;

use Cpsit\Typo3HandlebarsComponents\Domain\Factory\Page\PageFactory as BasePageFactory;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Frontend\Page\PageLayoutResolver;

class PageFactory extends BasePageFactory
{
    public function __construct(
        PageLayoutResolver $pageLayoutResolver,
        private readonly ResourceFactory $resourceFactory,
    ) {
        parent::__construct($pageLayoutResolver);
    }

}

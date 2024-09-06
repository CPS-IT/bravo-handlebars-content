<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Domain\Model\Dto;

readonly class Link
{
    public function __construct(
        public string $url = '',
        public string $label = '',
        public string $target = '')
    {
    }
}

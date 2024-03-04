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

/**
 * Link
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
readonly class Link
{
    public function __construct(
        public string $url = '',
        public string $label = '',
        public string $target = '')
    {
    }
}

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
class Link
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $target;

    public function __construct(string $url = '', string $label = '', string $target = null)
    {
        $this->url = $url;
        $this->label = $label;
        $this->target = $target;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }
}

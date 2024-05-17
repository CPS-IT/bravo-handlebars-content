<?php

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Dto;

class FieldProcessorConfiguration
{
    public function __construct(protected array $configuration = [])
    {
    }

    /**
     * @param string $name field name
     * @return array
     */
    public function get(string $name): array {
        return $this->configuration[$name] ?? [];
    }

    public function set(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}

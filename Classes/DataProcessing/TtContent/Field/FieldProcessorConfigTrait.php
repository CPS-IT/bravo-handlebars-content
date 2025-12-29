<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
trait FieldProcessorConfigTrait
{
    protected array $config = [];

    // @todo: remove this trait from all consuming classes
    public function withConfig(array $config): FieldProcessorInterface
    {
        $this->config = $config;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this;
    }
}

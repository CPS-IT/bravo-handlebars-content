<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Configuration;

use Cpsit\BravoHandlebarsContent\Configuration\SettingsInterface as SI;


/**
 * Extension
 *
 * provides configuration for the extension
 */
final class Extension
{
    public const KEY = SI::KEY;
    public const NAME = SI::NAME;
    public const VENDOR = SI::VENDOR_NAME;
}

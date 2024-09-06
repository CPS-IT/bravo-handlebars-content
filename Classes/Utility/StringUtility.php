<?php
/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Utility;

class StringUtility
{
    public static function hyphenToLowerCamelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($string)))));
    }
}

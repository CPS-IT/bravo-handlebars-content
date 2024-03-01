<?php

declare(strict_types=1);

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\BravoHandlebarsContent\Service;

use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Link;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;

/**
 * LinkService
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class LinkService
{
    public function __construct(
        private readonly ContentObjectRenderer $contentObjectRenderer,
    ) {
    }

    public function resolveTypoLink(string $typoLink): ?Link
    {
        $linkResult = $this->parseTypoLink($typoLink);

        if ($linkResult === null) {
            return null;
        }

        return new Link(
            $linkResult->getUrl(),
            (string)$linkResult->getAttribute('title'),
            $linkResult->getTarget(),
        );
    }

    public function parseTypoLink(string $typoLink): LinkResultInterface|null
    {
        $linkResult = $this->contentObjectRenderer->typoLink('|', [
            'parameter' => $typoLink,
            'returnLast' => 'result',
        ]);

        if (!($linkResult instanceof LinkResultInterface)) {
            return null;
        }

        return $linkResult;
    }
}

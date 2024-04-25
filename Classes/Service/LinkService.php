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

use Cpsit\BravoHandlebarsContent\Domain\Model\Dto\Link;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererAwareInterface;
use Cpsit\BravoHandlebarsContent\Traits\ContentRendererTrait;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;


final class LinkService implements ContentRendererAwareInterface
{

    use ContentRendererTrait;

    public function __construct(
        protected ContentObjectRenderer $contentObjectRenderer,
    ) {
    }

    public function resolveTypoLink(string $typoLink): LinkResultInterface
    {
        $linkResult = $this->parseTypoLink($typoLink);

        if (!($linkResult instanceof LinkResultInterface)) {
            $linkResult = new LinkResult('', '');
        }

        return $linkResult;
    }

    public function parseTypoLink(string $typoLink): LinkResultInterface|null
    {
        $linkResult = $this->contentObjectRenderer->typoLink('', [
            'parameter' => $typoLink,
            'returnLast' => 'result',
        ]);

        if (!($linkResult instanceof LinkResultInterface)) {
            return null;
        }

        return $linkResult;
    }

    public function linkResultToArray(LinkResultInterface $linkResult): array
    {
        return [
            'url' => $linkResult->getUrl(),
            'label' => $linkResult->getLinkText(),
            'title' => $linkResult->getAttribute('title') ?: '',
            'class' => $linkResult->getAttribute('class') ?: '',
            'target' => $linkResult->getTarget(),
            'type' => $linkResult->getType(),
            'additionalAttributes' => $linkResult->getAttributes(),
        ];
    }
}

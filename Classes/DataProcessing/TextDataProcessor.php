<?php

declare(strict_types=1);

namespace Cpsit\BravoHandlebarsContent\DataProcessing;

use Cpsit\BravoHandlebarsContent\Service\LinkService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

class TextDataProcessor implements DataProcessorInterface
{

    public function __construct(
        private readonly LinkService $linkService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {

        // resolve heder link

        $textHtml = $cObj->parseFunc(trim($processedData['data']['bodytext']), null, '< lib.parseFunc_RTE');


        $data = [
            'textHtml' => $textHtml,
            'id' => 'c' . $processedData['data']['uid'],
            'spaceBefore' => 'u-space-top:default',
        ];

        $headerLink = $this->linkService->resolveTypoLink($processedData['data']['header_link']);

        if ($headerLink) {
            $data['@headlines'] = [
                $this->findHeaderLayout((int)$processedData['data']['header_layout']) => [
                    'headline' => $processedData['data']['header'],
                    'url' => $headerLink->getUrl(),
                    'target' => $headerLink->getTarget()
                ],
            ];
        }

        return $data;
    }

    protected function findHeaderLayout(int $headerLayout = 0): string
    {
        if (empty($headerLayout)) {
            $headerLayout = 2;
        }
        return 'h' . $headerLayout;
    }

}



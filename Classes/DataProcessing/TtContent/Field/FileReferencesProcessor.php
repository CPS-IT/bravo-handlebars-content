<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;

/*
 * This file is part of the bravo handlebars content package.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
class FileReferencesProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public const DEFAULT_TABLE = 'tt_content';
    protected string $table = self::DEFAULT_TABLE;

    public function __construct(
        protected FileRepository $fileRepository
    )
    {
    }

    /**
     * Set a table to process. Default table is `tt_content` (and must not be set).
     * @param string $tableName
     * @return $this
     */
    public function forTable(string $tableName): self
    {
        $clone = clone $this;
        $clone->table = $tableName;
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        //@todo: We could use @MediaProvider, MediaProviderResponse and MediaVariablesResolver instead of this
        $files = [];
        if (empty($data[$fieldName] || empty($data['uid']))) {
            return $files;
        }

        $related = $this->fileRepository->findByRelation(
            $this->table,
            $fieldName,
            $data['uid']
        );
        /** @var FileReference $fileReference */
        foreach ($related as $fileReference) {
            if (!$fileReference instanceof FileReference) {
                continue;
            }
            $files[] = $fileReference;
        }
        $variables[$fieldName] = $files;

        return $variables;
    }
}

<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class FileCollectionsProcessor implements FieldProcessorInterface
{
    public function __construct(
        protected FileRepository $fileRepository,
        protected FileCollectionRepository $fileCollectionRepository
    ) {}

    public const KEY_TABLE = 'table';
    public const KEY_FIELD = 'field';
    public const TABLE_FILE_COLLECTION = 'sys_file_collection';
    public const FIELD_FILE_COLLECTIONS = 'file_collections';

    protected string $table = self::TABLE_FILE_COLLECTION;
    protected string $field = self::FIELD_FILE_COLLECTIONS;
    protected array $configuration = [
        self::KEY_TABLE => self::TABLE_FILE_COLLECTION,

    ];

    public function forTable(string $table): FieldProcessorInterface
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    public function forField(string $field): FieldProcessorInterface
    {
        $clone = clone $this;
        $clone->field = $field;
        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        $records = [];
        if (empty($data[$this->field])) {
            return $records;
        }

        $fileCollections = [];
        $fileCollectionIds = GeneralUtility::intExplode(
            delimiter: ',',
            string: $data[self::FIELD_FILE_COLLECTIONS]
        );
        foreach ($fileCollectionIds as $collectionId) {
            try {
                $collection = $this->fileCollectionRepository->findByUid($collectionId);
                if(null === $collection) {
                    continue;
                }
                $fileCollections[] = $collection;
            } catch (ResourceDoesNotExistException $e) {
                continue;
            }
        }
        $variables[$fieldName] = $fileCollections;

        return $variables;
    }
}

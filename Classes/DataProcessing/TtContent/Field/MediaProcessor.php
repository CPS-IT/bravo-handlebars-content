<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use RuntimeException;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class MediaProcessor implements FieldProcessorInterface
{
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
        $files = [];
        if (empty($data[$fieldName] || empty($data['uid']))) {
            return $files;
        }

        $related = $this->fileRepository->findByRelation(
            $this->table,
            $fieldName,
            $data['uid']
        );
        /** @var \TYPO3\CMS\Core\Resource\FileReference $fileReference */
        foreach ($related as $fileReference) {
            if (!$fileReference instanceof FileReference) {
                continue;
            }
            $files[] = $fileReference->getOriginalFile();
        }
        $variables[$fieldName] = $files;

        return $variables;
    }
}

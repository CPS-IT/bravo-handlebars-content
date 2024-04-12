<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
class RelatedRecordsProcessor implements FieldProcessorInterface
{
    use FieldProcessorConfigTrait;

    public function __construct(
        protected ContentObjectRenderer $contentObjectRenderer,
        protected ConnectionPool $connectionPool,
    ) {}

    public const KEY_TABLE = 'table';
    public const KEY_FIELD = 'field';
    public const DEFAULT_TABLE = 'tt_content';
    public const DEFAULT_FIELD = 'records';

    protected string $table = self::DEFAULT_TABLE;
    protected string $field = self::DEFAULT_FIELD;
    protected array $configuration = [
        self::KEY_TABLE => self::DEFAULT_TABLE,

    ];

    public function forTable(string $table): self
    {
        $clone = clone $this;
        $clone->table = $table;
        return $clone;
    }

    public function forField(string $field): self
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
        ;
        $connection = $this->connectionPool->getConnectionForTable($this->table);
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($this->table);
        $uids = GeneralUtility::intExplode(',', $data[$this->field]);
        try {
           $records = $queryBuilder->select('*')
                ->from($this->table)
                ->where(
                    $queryBuilder->expr()->in('uid', $uids)
                )->executeQuery()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            $message = $e->getMessage();
        } catch (\Doctrine\DBAL\Exception $e) {
            $message = $e->getMessage();
        }

        return $records;
    }
}

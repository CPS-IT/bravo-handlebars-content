<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
    public function __construct(
        protected ContentObjectRenderer $contentObjectRenderer,
        protected ConnectionPool $connectionPool,
    ) {}

    public const KEY_TABLE = 'table';
    public const DEFAULT_TABLE = 'tt_content';
    protected array $configuration = [
        self::KEY_TABLE => self::DEFAULT_TABLE
    ];

    public function forTable(string $table): FieldProcessorInterface
    {
        $this->configuration[self::KEY_TABLE] = $table;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        $connection = $this->connectionPool->getConnectionForTable($this->configuration[self::KEY_TABLE]);

        // todo find related records by table...
    }
}

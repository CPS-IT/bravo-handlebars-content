<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Map;

use Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\TtContentRecordInterface as TtContent;
use SplObjectStorage;

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
class CeTextDataMap implements DataMapInterface
{
    public const DEFAULT_FIELD_MAPS = [
        TtContent::FIELD_BODYTEXT => 'textHtml',
        TtContent::FIELD_HEADLINES => 'headlinesData',
        TtContent::FIELD_SPACE_BEFORE => 'spaceBefore',
        TtContent::FIELD_UID => 'id',
    ];

    protected SplObjectStorage $fieldMaps;

    public function __construct() {
        $this->fieldMaps = new SplObjectStorage();
        foreach (self::DEFAULT_FIELD_MAPS as $source => $target) {
            $this->fieldMaps->attach(new FieldMap($source, $target));
        }
    }

    /**
     * @return SplObjectStorage<FieldMap>
     */
    public function getFieldMaps(): SplObjectStorage
    {
        return $this->fieldMaps;
    }
}
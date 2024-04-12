<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\TtContent\Field;

use Cpsit\BravoHandlebarsContent\DataProcessing\FieldProcessorInterface;
use Cpsit\BravoHandlebarsContent\Service\ImageHelper;
use Cpsit\Typo3HandlebarsComponents\Data\MediaProvider;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\Media;
use Cpsit\Typo3HandlebarsComponents\Presenter\VariablesResolver\MediaVariablesResolver;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Media\OnlineMedia;
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
    public function __construct(
        protected MediaProvider          $mediaProvider,
        protected MediaVariablesResolver $mediaVariablesResolver
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function process(string $fieldName, array $data, array $variables): array
    {
        
        $response = $this->mediaProvider
            ->withMediaFieldName($fieldName)
            ->get($data);

        $media = $response->getFirstMedia();

        $file = $media->getOriginalFile();
        /** @var ImageHelper $imageHelper */
        $imageHelper = GeneralUtility::makeInstance(ImageHelper::class);
        $imageConfig = [
            'cropVariants' => [
                'desktop' => [
                    'maxWidth' => 1280
                ],
                'tablet' => [
                    'maxWidth' => 640
                ],
                'mobile' => [
                    'maxWidth' => 320
                ],
            ],
        ];
        $imageData = $imageHelper->process(
            '', $file, true, false, $imageConfig
        );
        
        // todo: We should move the following into the MediaVariablesResolver
        // note: MediaVariablesResolver processes only the first media
        // we assume that the content element will not be used with multiple image/media
        $pictureData = $this->mediaVariablesResolver->withMediaResponse($response)->resolve();
        if (!$media instanceof OnlineMedia) {
            $variables[$fieldName]['pictureData'] = $pictureData;
        }
        if ($media instanceof OnlineMedia) {
            $variables[$fieldName] = [
                'onlineMedia' => [
                    'pictureData' => $variables[$fieldName]['pictureData'] = $pictureData,
                    'publicUrl' => $media->getPublicUrl(),
                    'previewImage' => $media->getPreviewImage(),
                    'onlineMediaId' => $media->getOnlineMediaId(),
                    'title' => $media->getProperty('title'),
                    'allow' => true,
                    'accessibilityData' => [
                        //@todo: localized text for play button
                        'accessibility' => 'Video starten'
                    ]
                ]
            ];
        }
        $variables['originalFirstMedia'] = $media;
        return $variables;
    }
}

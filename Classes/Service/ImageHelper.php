<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 *
 */

namespace Cpsit\BravoHandlebarsContent\Service;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Extbase\Service\ImageService;


class ImageHelper
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /**
     * Process image file reference
     *
     * Configuration options:
     * - cropVariants: select a cropping variant, in case multiple croppings have been specified or stored in FileReference
     *  - width: width of the image. This can be a numeric value representing the fixed width of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.
     *  - height: height of the image. This can be a numeric value representing the fixed height of the image in pixels. But you can also perform simple calculations by adding "m" or "c" to the value. See imgResource.width for possible options.
     *  - minWidth: minimum width of the image
     *  - minHeight: minimum height of the image
     *  - maxWidth: maximum width of the image
     *  - maxHeight: maximum height of the image
     *  - fileExtension
     *
     * @param string $src
     * @param object|null $image
     * @param bool $treatIdAsReference
     * @param array $config
     * @return void
     * @throws \Exception
     */
    public function process(
        string $src = '',
        ?object $image = null,
        bool $treatIdAsReference = true,
        bool $absolute = false,
        array $config = []
    ): array {
        $processedImages = [];

        if (($src === '' && $image === null) || ($src !== '' && $image !== null)) {
            throw new \InvalidArgumentException('You must either specify a string src or a File object.', 1382284106);
        }

        try {
            $image = $this->imageService->getImage($src, $image, $treatIdAsReference);
            if ($image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }

            if (is_array($cropString)) {
                $cropString = json_encode($cropString);
            }
            $cropVariantCollection = CropVariantCollection::create((string)$cropString);
            if (!isset($config['cropVariants']) || !is_array($config['cropVariants'])) {
                $config['cropVariants']['default'] = [];
            }
            foreach ($config['cropVariants'] as $cropVariant => $processingInstructions) {
                $cropArea = $cropVariantCollection->getCropArea($cropVariant);
                if ($image->hasProperty('uid') && $image->getProperty('uid')) {
                    $uid = $image->getProperty('uid');
                }
                $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
                $processedImages[$cropVariant] = [
                    'src' => $this->imageService->getImageUri($processedImage, $absolute),
                    'width' => $processedImage->getProperty('width'),
                    'height' => $processedImage->getProperty('height'),
                    'alt' => $image->hasProperty('alternative') ? $image->getProperty('alternative') : '',
                    'title' => $image->hasProperty('title') ? $image->getProperty('title') : '',
                ];
            }

        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
            throw new \Exception($e->getMessage(), 1509741911, $e);
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
            throw new \Exception($e->getMessage(), 1509741912, $e);
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
            throw new \Exception($e->getMessage(), 1509741913, $e);
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
            throw new \Exception($e->getMessage(), 1509741914, $e);
        }
        return $processedImages;
    }
}

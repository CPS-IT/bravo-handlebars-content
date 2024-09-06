<?php

namespace Cpsit\BravoHandlebarsContent\DataProcessing\Media;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait OnlineMediaProcessorTrait
{
    protected false|OnlineMediaHelperInterface $onlineMediaHelper = false;

    protected function getOriginalFile(FileInterface $file): File|FileInterface
    {
        if ($file instanceof FileReference) {
            return $file->getOriginalFile();
        }
        return $file;
    }

    protected function getPreviewImageFromFile(FileInterface $file): string
    {
        $orgFile = $this->getOriginalFile($file);
        return $this->getOnlineMediaHelper($file)->getPreviewImage($orgFile);
    }

    protected function getVideoIdFromFile(FileInterface $file): string
    {
        $orgFile = $this->getOriginalFile($file);
        return $this->getOnlineMediaHelper($file)->getOnlineMediaId($orgFile);
    }

    /**
     * Get online media helper
     *
     * @return false|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper(FileInterface $file): false|OnlineMediaHelperInterface
    {
        if ($this->onlineMediaHelper === false) {
            $orgFile = $file;
            if ($orgFile instanceof FileReference) {
                $orgFile = $orgFile->getOriginalFile();
            }
            if ($orgFile instanceof File) {
                $this->onlineMediaHelper = GeneralUtility::makeInstance(OnlineMediaHelperRegistry::class)->getOnlineMediaHelper($orgFile);
            } else {
                $this->onlineMediaHelper = false;
            }
        }
        return $this->onlineMediaHelper;
    }
}

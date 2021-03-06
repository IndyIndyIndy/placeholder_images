<?php
namespace ChristianEssl\PlaceholderImages\Resource\Placeholder;

/***
 *
 * This file is part of the "PlaceholderImages" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christian Eßl <indy.essl@gmail.com>, https://christianessl.at
 *
 ***/

use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract class for processing placeholder images
 */
abstract class AbstractProcessor
{

    /**
     * @param ImageSettings $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    abstract public function processFile(ImageSettings $imageSettings, $targetFolderIdentifier);

    /**
     * @param string $image
     * @param string $fileName
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function getFile($image, $fileName, $targetFolderIdentifier)
    {
        $targetFolder = $this->getTargetFolder($targetFolderIdentifier);
        $file = $this->findExistingFileForImage($image, $targetFolder);

        if (!$file instanceof File) {
            $file = $this->createNewFile($image, $fileName, $targetFolder);
        }

        return $file;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    protected function isValidImage($filePath) : bool
    {
        return exif_imagetype($filePath) == IMAGETYPE_GIF ||
            exif_imagetype($filePath) == IMAGETYPE_PNG ||
            exif_imagetype($filePath) == IMAGETYPE_JPEG;
    }

    /**
     * @param string $image
     * @param Folder $targetFolder
     *
     * @return File|null
     */
    protected function findExistingFileForImage($image, Folder $targetFolder)
    {
        $fileIndexRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\Index\FileIndexRepository::class);
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        $file = null;
        $fileHash = sha1($image);

        if (!$image) {
            return $file;
        }

        $files = $fileIndexRepository->findByContentHash($fileHash);
        if (!empty($files)) {
            foreach ($files as $fileIndexEntry) {
                if (
                    $fileIndexEntry['folder_hash'] === $targetFolder->getHashedIdentifier()
                    && (int)$fileIndexEntry['storage'] === $targetFolder->getStorage()->getUid()
                ) {
                    try {
                        $file = $resourceFactory->getFileObject($fileIndexEntry['uid'], $fileIndexEntry);
                        break;
                    } catch(FileDoesNotExistException $e) {
                        continue;
                    }
                }
            }
        }
        return $file;
    }

    /**
     * @param string $image
     * @param string $fileName
     * @param Folder $targetFolder
     *
     * @return File|null
     */
    protected function createNewFile($image, $fileName, Folder $targetFolder)
    {
        $file = null;
        $temporaryFile = GeneralUtility::tempnam('placeholder_image');
        GeneralUtility::writeFileToTypo3tempDir($temporaryFile, $image);

        if ($this->isValidImage($temporaryFile)) {
            $file = $targetFolder->addFile($temporaryFile, $fileName, DuplicationBehavior::RENAME);
        }

        GeneralUtility::unlink_tempfile($temporaryFile);

        return $file;
    }

    /**
     * @param string $targetFolderIdentifier
     *
     * @return Folder
     */
    protected function getTargetFolder($targetFolderIdentifier) : Folder
    {
        $targetFolder = null;
        if ($targetFolderIdentifier) {
            try {
                $targetFolder = ResourceFactory::getInstance()->getFolderObjectFromCombinedIdentifier($targetFolderIdentifier);
            } catch (\Exception $e) {
                $targetFolder = null;
            }
        }
        if ($targetFolder === null) {
            $targetFolder = $GLOBALS['BE_USER']->getDefaultUploadFolder();
        }
        return $targetFolder;
    }

    /**
     * @param ImageSettings $settings
     *
     * @return string
     */
    protected function getFileName(ImageSettings $settings) : string
    {
        $title = 'placeholder';
        if (strlen($settings->getPlaceholder()) > 0) {
            $title = $settings->getPlaceholder();
        }
        $fileName = $title . ' ' . $settings->getWidth() . 'x' . $settings->getHeight();
        return $fileName . '.' . $settings->getFormat();
    }

}
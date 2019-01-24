<?php
namespace ChristianEssl\PlaceholderImages\File;

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
 * Download the placeholder and add it to FAL
 */
class PlaceholderDownloader
{

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function getFile($imageSettings, $targetFolderIdentifier)
    {
        $targetFolder = $this->getTargetFolder($targetFolderIdentifier);
        $url = $this->buildPlaceholderDotComURL($imageSettings);
        $image = $this->downloadImage($url);

        $file = $this->findExistingFileForImage($image, $targetFolder);

        if (!$file) {
            $fileName = $this->getFileName($imageSettings);
            $temporaryFile = GeneralUtility::tempnam('placehoder_image');
            GeneralUtility::writeFileToTypo3tempDir($temporaryFile, $image);
            $file = $targetFolder->addFile($temporaryFile, $fileName, DuplicationBehavior::RENAME);
            GeneralUtility::unlink_tempfile($temporaryFile);
        }

        return $file;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function downloadImage($url) : string
    {
        return GeneralUtility::getUrl($url);
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

                    }
                }
            }
        }
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
     * @param array $settings
     *
     * @return string
     */
    protected function getFileName($settings) : string
    {
        $title = 'placeholder';
        if (isset($settings['placeholder']) && strlen($settings['placeholder']) > 0) {
            $title = $settings['placeholder'];
        }
        $fileName = $title . ' ' . $settings['width'] . 'x' . $settings['height'];
        return $fileName . '.' . $settings['format'];
    }

    /**
     * @param array $settings
     *
     * @return string
     */
    protected function buildPlaceholderDotComURL($settings) : string
    {
        $scheme = GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
        $url = $scheme.'via.placeholder.com/';

        // dimensions
        $width = $settings['width'];
        $height = $settings['height'];
        $url .= $width . 'x' . $height;

        // colors
        if (isset($settings['bgcolor']) && isset($settings['textcolor'])) {
            $bcolor = str_replace('#', '', $settings['bgcolor']);
            $textcolor = str_replace('#', '', $settings['textcolor']);
            $url .= '/' . $bcolor . '/'.$textcolor;
        }

        // file extension
        if (isset($settings['format'])) {
            $url .= '.' . $settings['format'];
        }

        // file extension
        if (isset($settings['placeholder']) && strlen($settings['placeholder']) > 0) {
            $url .= '?text=' . urlencode($settings['placeholder']);
        }

        return $url;
    }

}
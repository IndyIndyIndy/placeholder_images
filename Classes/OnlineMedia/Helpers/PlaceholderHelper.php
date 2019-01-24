<?php
namespace ChristianEssl\PlaceholderImages\OnlineMedia\Helpers;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\AbstractOnlineMediaHelper;
use TYPO3\CMS\Core\Utility\Exception\NotImplementedMethodException;

/**
 * Create the placeholder image
 */
class PlaceholderHelper extends AbstractOnlineMediaHelper
{

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * Get public url
     *
     * @param File $file
     * @param bool $relativeToCurrentScript
     * @return string|null
     */
    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        throw new NotImplementedMethodException();
    }

    /**
     * Get local absolute file path to preview image
     *
     * @param File $file
     * @return string
     */
    public function getPreviewImage(File $file)
    {
        throw new NotImplementedMethodException();
    }

    /**
     * @param string $url
     * @param Folder $targetFolder
     *
     * @return null
     */
    public function transformUrlToFile($url, Folder $targetFolder)
    {
        $file = $this->findExistingFileByOnlineMediaId($url, $targetFolder, $this->extension);

        if ($file === null) {
            $file = $this->createNewFile($targetFolder, $this->fileName, $url);
        }
        return $file;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Get meta data
     *
     * @param File $file
     * @return array|null
     */
    public function getMetaData(File $file)
    {
        throw new NotImplementedMethodException();
    }
}
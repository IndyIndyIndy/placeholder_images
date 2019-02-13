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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Download the placeholder and add it to FAL
 */
class PlaceholderComProcessor extends AbstractProcessor
{

    /**
     * @param ImageSettings $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile(ImageSettings $imageSettings, $targetFolderIdentifier)
    {
        $fileName = $this->getFileName($imageSettings);
        $url = $this->buildPlaceholderDotComURL($imageSettings);
        $image = GeneralUtility::getUrl($url);

        return $this->getFile($image, $fileName, $targetFolderIdentifier);
    }

    /**
     * @param ImageSettings $settings
     *
     * @return string
     */
    protected function buildPlaceholderDotComURL(ImageSettings $settings) : string
    {
        $scheme = GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
        $url = $scheme.'via.placeholder.com/';

        // dimensions
        $width = $settings->getWidth();
        $height = $settings->getHeight();
        $url .= $width . 'x' . $height;

        // colors
        if (strlen($settings->getBgColor()) > 0 && strlen($settings->getTextColor()) > 0) {
            $bgcolor = str_replace('#', '', $settings->getBgColor());
            $textcolor = str_replace('#', '', $settings->getTextColor());
            $url .= '/' . $bgcolor . '/'.$textcolor;
        }

        // file extension
        if (strlen($settings->getFormat()) > 0) {
            $url .= '.' . $settings->getFormat();
        }

        // file extension
        if (strlen($settings->getPlaceholder()) > 0) {
            $url .= '?text=' . urlencode($settings->getPlaceholder());
        }

        return $url;
    }

}
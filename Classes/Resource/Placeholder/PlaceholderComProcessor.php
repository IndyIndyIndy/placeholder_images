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
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile($imageSettings, $targetFolderIdentifier)
    {
        $fileName = $this->getFileName($imageSettings);
        $url = $this->buildPlaceholderDotComURL($imageSettings);
        $image = GeneralUtility::getUrl($url);
        return $this->getFile($image, $fileName, $targetFolderIdentifier);
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
            $bgcolor = str_replace('#', '', $settings['bgcolor']);
            $textcolor = str_replace('#', '', $settings['textcolor']);
            $url .= '/' . $bgcolor . '/'.$textcolor;
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
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

use ChristianEssl\PlaceholderImages\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Download the placeholder image from a custom source set in the extension configuration
 */
class CustomSourceProcessor extends AbstractProcessor
{

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile($imageSettings, $targetFolderIdentifier)
    {
        $configuration = ConfigurationUtility::getExtensionConfiguration();

        $fileName = $this->getFileName($imageSettings);
        $url = $this->setUrlParameters($configuration['customSourceUrl'], $imageSettings);
        $image = GeneralUtility::getUrl($url);

        // @todo validate if image is an acutal image

        return $this->getFile($image, $fileName, $targetFolderIdentifier);
    }

    /**
     * @param string $urlTemplate
     * @param array $settings
     *
     * @return string
     */
    protected function setUrlParameters($urlTemplate, $settings) : string
    {
        return str_replace([
            '{width}',
            '{height}',
            '{bgcolor}',
            '{textcolor}',
            '{format}',
            '{text}',
        ], [
            $settings['width'],
            $settings['height'],
            str_replace('#', '', $settings['bgcolor']),
            str_replace('#', '', $settings['textcolor']),
            $settings['format'],
            urlencode($settings['placeholder']),
        ], $urlTemplate);
    }
}
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
     * @param ImageSettings $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile(ImageSettings $imageSettings, $targetFolderIdentifier)
    {
        $configuration = ConfigurationUtility::getExtensionConfiguration();

        $fileName = $this->getFileName($imageSettings);
        $url = $this->setUrlParameters($configuration['customSourceUrl'], $imageSettings);
        $image = GeneralUtility::getUrl($url);

        return $this->getFile($image, $fileName, $targetFolderIdentifier);
    }

    /**
     * @param string $urlTemplate
     * @param ImageSettings $settings
     *
     * @return string
     */
    protected function setUrlParameters($urlTemplate, ImageSettings $settings) : string
    {
        return str_replace([
            '{width}',
            '{height}',
            '{bgcolor}',
            '{textcolor}',
            '{format}',
            '{text}',
        ], [
            $settings->getWidth(),
            $settings->getHeight(),
            str_replace('#', '', $settings->getBgColor()),
            str_replace('#', '', $settings->getTextColor()),
            $settings->getFormat(),
            urlencode($settings->getPlaceholder()),
        ], $urlTemplate);
    }
}
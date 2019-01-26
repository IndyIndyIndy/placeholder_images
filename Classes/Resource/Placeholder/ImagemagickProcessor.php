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
use TYPO3\CMS\Core\Utility\Exception\NotImplementedMethodException;

/**
 * Generate the placeholder locally with imagemagick
 */
class ImagemagickProcessor extends AbstractProcessor
{

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile($imageSettings, $targetFolderIdentifier)
    {
        throw new NotImplementedMethodException();
    }

}
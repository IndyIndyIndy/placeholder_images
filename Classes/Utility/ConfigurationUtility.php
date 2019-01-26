<?php
namespace ChristianEssl\PlaceholderImages\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Checks the extension configuration
 */
class ConfigurationUtility
{

    /**
     * @return bool
     */
    public static function isCurrentTYPO3ContextAllowed() : bool
    {
        $currentContext = self::getCurrentContext();
        $allowedContexts = self::getAllowedContexts();
        return in_array($currentContext, $allowedContexts);
    }

    /**
     * @return mixed[]
     */
    public static function getExtensionConfiguration() : array
    {
        try {
            if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000) {
                return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)
                    ->get('placeholder_images');
            } else {
                return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['placeholder_images']);
            }
        } catch (\TYPO3\CMS\Core\Exception $e) {
            return [];
        }
    }

    /**
     * @return string
     */
    protected static function getCurrentContext() : string
    {
        return GeneralUtility::getApplicationContext()->__toString();
    }

    /**
     * @return string[]
     */
    protected static function getAllowedContexts() : array
    {
        $configuration = self::getExtensionConfiguration();
        if (isset($configuration['typo3Contexts'])) {
            return GeneralUtility::trimExplode(",", $configuration['typo3Contexts']);
        }
        return [];
    }

}
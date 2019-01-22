<?php
namespace ChristianEssl\PlaceholderImages\Service;

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

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


/**
 * Checks the extension configuration
 */
class ConfigurationService
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
    protected static function getExtensionConfiguration() : array
    {
        try {
            $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('placeholder_images');
            return $configuration;
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
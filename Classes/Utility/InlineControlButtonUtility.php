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

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility for placing the placeholder button in the InlineControlContainer
 */
class InlineControlButtonUtility
{
    /**
     * @param array $inlineData
     * @param string $nameObject
     * @param string $objectPrefix
     * @param Folder $folder
     *
     * @return string
     */
    public static function getPlaceholderButton($inlineData, $nameObject, $objectPrefix, Folder $folder) : string
    {
        $buttonText = self::getTranslation('tx_placeholderimages.button.text');
        $buttonSubmitText = self::getTranslation('tx_placeholderimages.button.submit');
        $widthText = self::getTranslation('tx_placeholderimages.image.width');
        $heightText = self::getTranslation('tx_placeholderimages.image.height');
        $formatText = self::getTranslation('tx_placeholderimages.image.format');
        $placeholderText = self::getTranslation('tx_placeholderimages.image.text');
        $bgcolorText = self::getTranslation('tx_placeholderimages.image.bgcolor');
        $textcolorText = self::getTranslation('tx_placeholderimages.image.textcolor');

        $configuration = ConfigurationUtility::getExtensionConfiguration();

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        return '
						<span class="btn btn-default t3js-placeholder-add-btn ' . $inlineData['config'][$nameObject]['md5'] . '"
							data-file-irre-object="' . htmlspecialchars($objectPrefix) . '"
							data-target-folder="' . htmlspecialchars($folder->getCombinedIdentifier()) . '"
							title="' . $buttonText . '"
							
							data-width-text="' . $widthText . '"
							data-height-text="' . $heightText . '"
							data-format-text="' . $formatText . '"
							data-placeholder-text="' . $placeholderText . '"	
							data-bgcolor-text="' . $bgcolorText . '"	
							data-textcolor-text="' . $textcolorText . '"							
							
							data-width-default="' . $configuration['defaultWidth'] . '"
							data-height-default="' . $configuration['defaultHeight'] . '"
							data-format-default="' . $configuration['defaultFormat'] . '"
							data-placeholder-default="' . $configuration['defaultText'] . '"
							data-bgcolor-default="' . $configuration['defaultBGColor'] . '"
							data-textcolor-default="' . $configuration['defaultTextColor'] . '"
							
							data-btn-submit="' . $buttonSubmitText . '"
							>
							' . $iconFactory->getIcon('actions-system-extension-configure', Icon::SIZE_SMALL)->render() . '
							' . $buttonText . '</span>';
    }

    /**
     * @return void
     */
    public static function loadJavaScript()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/PlaceholderImages/PlaceholderUploader', 'function(PlaceholderUploader) {
			PlaceholderUploader.init();
		}');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/PlaceholderImages/PlaceholderFormBuilder');
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected static function getTranslation($key) : string
    {
        return htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:placeholder_images/Resources/Private/Language/locallang_db.xlf:'.$key));
    }

}
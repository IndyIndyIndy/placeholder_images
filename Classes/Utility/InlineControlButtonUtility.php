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
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility for placing the placeholder button in the InlineControlContainer
 */
class InlineControlButtonUtility
{

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    public function __construct()
    {
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * @param array $inlineData
     * @param string $nameObject
     * @param string $objectPrefix
     * @param Folder $folder
     *
     * @return string
     */
    public function getPlaceholderButton($inlineData, $nameObject, $objectPrefix, Folder $folder) : string
    {
        $buttonText = $this->getTranslation('tx_placeholderimages.button.text');
        $buttonSubmitText = $this->getTranslation('tx_placeholderimages.button.submit');
        $widthText = $this->getTranslation('tx_placeholderimages.image.width');
        $heightText = $this->getTranslation('tx_placeholderimages.image.height');
        $formatText = $this->getTranslation('tx_placeholderimages.image.format');
        $placeholderText = $this->getTranslation('tx_placeholderimages.image.text');
        $bgcolorText = $this->getTranslation('tx_placeholderimages.image.bgcolor');
        $textcolorText = $this->getTranslation('tx_placeholderimages.image.textcolor');

        $configuration = ConfigurationUtility::getExtensionConfiguration();

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
							' . $this->iconFactory->getIcon('actions-system-extension-configure', Icon::SIZE_SMALL)->render() . '
							' . $buttonText . '</span>';
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getTranslation($key) : string
    {
        return htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:placeholder_images/Resources/Private/Language/locallang_db.xlf:'.$key));
    }

}
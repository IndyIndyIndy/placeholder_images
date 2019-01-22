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

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for placeholder images button
 */
class PlaceholderService
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
        $buttonSubmit = $this->getTranslation('tx_placeholderimages.button.submit');
        $width = $this->getTranslation('tx_placeholderimages.image.width');
        $height = $this->getTranslation('tx_placeholderimages.image.height');
        $format = $this->getTranslation('tx_placeholderimages.image.format');
        $placeholder = $this->getTranslation('tx_placeholderimages.image.text');

        return '
						<span class="btn btn-default t3js-placeholder-add-btn ' . $inlineData['config'][$nameObject]['md5'] . '"
							data-file-irre-object="' . htmlspecialchars($objectPrefix) . '"
							data-target-folder="' . htmlspecialchars($folder->getCombinedIdentifier()) . '"
							title="' . $buttonText . '"
							data-text-width="' . $width . '"
							data-text-height="' . $height . '"
							data-text-format="' . $format . '"
							data-text-placeholder="' . $placeholder . '"
							data-btn-submit="' . $buttonSubmit . '"
							>
							' . $this->iconFactory->getIcon('actions-system-extension-configure', Icon::SIZE_SMALL)->render() . '
							' . $buttonText . '</span>';
    }

    /**
     * @return string
     */
    protected function getTranslation($key) : string
    {
        return htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:placeholder_images/Resources/Private/Language/locallang_db.xlf:'.$key));
    }

}
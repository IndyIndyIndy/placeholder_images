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

use TYPO3\CMS\Core\Imaging\GraphicalFunctions;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generate the placeholder locally
 */
class LocalImageProcessor extends AbstractProcessor
{
    /**
     * @var GraphicalFunctions
     */
    protected $graphicalFunctions = null;

    /**
     * @var integer
     */
    protected $imageWidth = null;

    /**
     * @var integer
     */
    protected $imageHeight = null;

    /**
     * @var array
     */
    protected $bgcolor = null;

    /**
     * @var array
     */
    protected $textcolor = null;

    /**
     * @var string
     */
    protected $text = null;

    public function __construct()
    {
        $this->graphicalFunctions = GeneralUtility::makeInstance(GraphicalFunctions::class);
    }

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile($imageSettings, $targetFolderIdentifier)
    {
        $fileName = $this->getFileName($imageSettings);
        $image = $this->generateImage($imageSettings);
        
        return $this->getFile($image, $fileName, $targetFolderIdentifier);
    }

    /**
     * @param array $imageSettings
     *
     * @return string
     */
    protected function generateImage($imageSettings) : string
    {
        $this->loadSettings($imageSettings);

        $image = @imagecreate($this->imageWidth, $this->imageHeight);
        imagecolorallocate($image, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);

        $fontFile = ExtensionManagementUtility::extPath('placeholder_images') . 'Resources/Private/Font/vera.ttf';
        $fontSize = $this->getFontSize($fontFile);
        list($x, $y) = $this->getTextOffset($fontFile, $fontSize);

        $fontColor = imagecolorallocate($image, $this->textcolor[0], $this->textcolor[1], $this->textcolor[2]);
        @imagettftext(
            $image,
            $fontSize,
            0,
            $x,
            $y,
            $fontColor,
            $fontFile,
            $this->text
        );

        $format = $imageSettings['format'];
        switch($format) {
            case 'png':
                imagepng($image);
                break;
            case 'jpg':
                imagejpeg($image);
                break;
            case 'gif':
                imagegif($image);
                break;
        }

        imagedestroy($image);
        $image = ob_get_contents();
        ob_end_clean();
        return $image;
    }

    /**
     * @param array $imageSettings
     *
     * @return void
     */
    protected function loadSettings($imageSettings)
    {
        $this->bgcolor = $this->graphicalFunctions->convertColor($imageSettings['bgcolor']);
        $this->textcolor = $this->graphicalFunctions->convertColor($imageSettings['textcolor']);
        $this->imageWidth = $imageSettings['width'];
        $this->imageHeight = $imageSettings['height'];
        $this->text = $imageSettings['placeholder'];
    }

    /**
     * @param string $fontFile
     *
     * @return int
     */
    protected function getFontSize($fontFile) : int
    {
        $size = 10;

        for($i = 999; $i > 0; $i--) {
            $textSize = imagettfbbox($i, 0, $fontFile, $this->text);
            if(
                (abs($textSize[2] - $textSize[0]) < $this->imageWidth) &&
                (abs($textSize[7] - $textSize[1]) < $this->imageHeight)
            ) {
                $size = round($i * 0.85);
                break;
            }
        }

        return $size;
    }

    /**
     * @param string $fontFile
     * @param integer $fontSize
     *
     * @return array
     */
    protected function getTextOffset($fontFile, $fontSize) : array
    {
        $textSize = imagettfbbox($fontSize, 0, $fontFile, $this->text);
        $x = $textSize[0] + ($this->imageWidth / 2) - ($textSize[4] / 2);
        $y = $textSize[1] + ($this->imageHeight / 2) - ($textSize[5] / 2);
        return [$x, $y];
    }
}
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

use Doctrine\Common\Util\Debug;
use TYPO3\CMS\Core\Imaging\GraphicalFunctions;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

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
     * @param ImageSettings $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    public function processFile(ImageSettings $imageSettings, $targetFolderIdentifier)
    {
        $fileName = $this->getFileName($imageSettings);
        $image = $this->generateImage($imageSettings);
        
        return $this->getFile($image, $fileName, $targetFolderIdentifier);
    }

    /**
     * @param ImageSettings $imageSettings
     *
     * @return string
     */
    protected function generateImage(ImageSettings $imageSettings) : string
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

        $format = $imageSettings->getFormat();
        return $this->getImageOutput($image, $format);
    }

    /**
     * @param resource $image
     * @param string $format
     *
     * @return string
     */
    protected function getImageOutput($image, $format)  : string
    {
        switch($format) {
            case 'png':
                imagepng($image);
                break;
            case 'jpg':
                imagejpeg($image);
                break;
            case 'gif':
            default:
                imagegif($image);
                break;
        }

        imagedestroy($image);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * @param ImageSettings $imageSettings
     *
     * @return void
     */
    protected function loadSettings(ImageSettings $imageSettings)
    {
        $this->bgcolor = $this->graphicalFunctions->convertColor($imageSettings->getBgColor());
        $this->textcolor = $this->graphicalFunctions->convertColor($imageSettings->getTextColor());
        $this->imageWidth = $imageSettings->getWidth();
        $this->imageHeight = $imageSettings->getHeight();
        $this->text = $imageSettings->getPlaceholder();

        if (strlen($this->text) == 0) {
            $this->text = $this->imageWidth . 'x' . $this->imageHeight;
        }
    }

    /**
     * Start large and decrease font size until the text fits the allowed margin
     *
     * @param string $fontFile
     *
     * @return int
     */
    protected function getFontSize($fontFile) : int
    {
        $size = 10;
        $maxAllowedTextWidth = $this->imageWidth * 0.75;
        $maxAllowedTextHeight = $this->imageHeight * 0.35;

        for($i = 999; $i > 0; $i--) {
            $textSize = imagettfbbox($i, 0, $fontFile, $this->text);
            if(
                ($this->getTextWidth($textSize) < $maxAllowedTextWidth) &&
                ($this->getTextHeight($textSize) < $maxAllowedTextHeight)
            ) {
                $size = round($i);
                break;
            }
        }

        return $size;
    }

    /**
     * @param $textSize
     *
     * @return int
     */
    protected function getTextWidth($textSize) : int
    {
        return abs($textSize[2] - $textSize[0]);
    }

    /**
     * @param $textSize
     *
     * @return int
     */
    protected function getTextHeight($textSize) : int
    {
        return abs($textSize[7] - $textSize[1]);
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

        $x = floor(($this->imageWidth - $textSize[4]) / 2);
        $y = floor(($this->imageHeight - $textSize[5]) / 2);

        return [$x, $y];
    }
}
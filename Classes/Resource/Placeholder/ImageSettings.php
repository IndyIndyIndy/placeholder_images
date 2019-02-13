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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Image settings for processing
 */
class ImageSettings
{
    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var string
     */
    protected $format = 'gif';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var string
     */
    protected $bgColor = '';

    /**
     * @var string
     */
    protected $textColor = '';

    public function __construct(ServerRequestInterface $request)
    {
        $this->width = (int) $request->getParsedBody()['width'];
        $this->height = (int) $request->getParsedBody()['height'];
        $this->format = $request->getParsedBody()['format'];
        $this->placeholder = $request->getParsedBody()['placeholder'];
        $this->bgColor = $request->getParsedBody()['bgcolor'];
        $this->textColor = $request->getParsedBody()['textcolor'];

        if (!$this->width) {
            $this->width = $this->height;
        }
        if (!$this->height) {
            $this->height = $this->width;
        }
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return string
     */
    public function getBgColor(): string
    {
        return $this->bgColor;
    }

    /**
     * @return string
     */
    public function getTextColor(): string
    {
        return $this->textColor;
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->width > 0 && $this->height > 0;
    }

}
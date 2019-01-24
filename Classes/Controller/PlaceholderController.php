<?php
namespace ChristianEssl\PlaceholderImages\Controller;

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

use ChristianEssl\PlaceholderImages\File\PlaceholderDownloader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles generating and uploading placeholder images
 */
class PlaceholderController
{
    /**
     * AJAX endpoint for creating an image and storing it as a sys_file record
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createAction(ServerRequestInterface $request): ResponseInterface
    {
        $targetFolderIdentifier = $request->getParsedBody()['targetFolder'];
        $width = (int) $request->getParsedBody()['width'];
        $height = (int) $request->getParsedBody()['height'];
        $format = $request->getParsedBody()['format'];
        $placeholder = $request->getParsedBody()['placeholder'];
        $bgcolor = $request->getParsedBody()['bgcolor'];
        $textcolor = $request->getParsedBody()['textcolor'];

        if (!$width) {
            $width = $height;
        }
        if (!$height) {
            $height = $width;
        }

        $imageSettings = [
            'width' => $width,
            'height' => $height,
            'format' => $format,
            'placeholder' => $placeholder,
            'bgcolor' => $bgcolor,
            'textcolor' => $textcolor,
        ];

        if (!empty($width)) {
            $data = [];
            $file = $this->getFile($imageSettings, $targetFolderIdentifier);
            if ($file !== null) {
                $data['file'] = $file->getUid();
            } else {
                $data['error'] = $this->getTranslation('tx_placeholderimages.image.error.unknown_error');
            }
            return new JsonResponse($data);
        }
        return new JsonResponse(['error' => $this->getTranslation('tx_placeholderimages.image.error.empty_request')]);
    }

    /**
     * @param array $imageSettings
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    protected function getFile($imageSettings, $targetFolderIdentifier)
    {
        $placeholderDownloader = GeneralUtility::makeInstance(PlaceholderDownloader::class);
        $placeholderDownloader->getFile($imageSettings, $targetFolderIdentifier);
        return $placeholderDownloader->getFile($imageSettings, $targetFolderIdentifier);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getTranslation($key) : string
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:placeholder_images/Resources/Private/Language/locallang_db.xlf:'.$key);
    }

}
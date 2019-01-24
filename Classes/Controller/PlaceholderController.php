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

use ChristianEssl\PlaceholderImages\OnlineMedia\Helpers\PlaceholderHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
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
            $file = $this->createFileRelation($imageSettings, $targetFolderIdentifier);
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
    protected function createFileRelation($imageSettings, $targetFolderIdentifier)
    {
        $targetFolder = $this->getTargetFolder($targetFolderIdentifier);

        $title = 'Placeholder Image';
        if (isset($imageSettings['placeholder']) && strlen($imageSettings['placeholder']) > 0) {
            $title = $imageSettings['placeholder'];
        }
        $fileName = $title . ' ' . $imageSettings['width'] . 'x' . $imageSettings['height'];

        $url = $this->buildPlaceholderDotComURL($imageSettings);
        $placeholderHelper = GeneralUtility::makeInstance(PlaceholderHelper::class, 'placeholder');
        $placeholderHelper->setFileName($fileName);

        return $placeholderHelper->transformUrlToFile($url, $targetFolder);
    }


    /**
     * @param array $settings
     *
     * @return string
     */
    protected function buildPlaceholderDotComURL($settings) : string
    {
        $scheme = GeneralUtility::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
        $url = $scheme.'via.placeholder.com/';

        // dimensions
        $width = $settings['width'];
        $height = $settings['height'];
        $url .= $width.'x'.$height;

        // colors
        if (isset($settings['bgcolor']) && isset($settings['textcolor'])) {
            $bcolor = str_replace('#', '', $settings['bgcolor']);
            $textcolor = str_replace('#', '', $settings['textcolor']);
            $url .= '/'.$bcolor.'/'.$textcolor;
        }

        // file extension
        if (isset($settings['format'])) {
            $url .= '.'.$settings['format'];
        }

        // file extension
        if (isset($settings['placeholder']) && strlen($settings['placeholder']) > 0) {
            $url .= '?text='.urlencode($settings['placeholder']);
        }

        return $url;
    }

    /**
     * @param string $targetFolderIdentifier
     *
     * @return Folder
     */
    protected function getTargetFolder($targetFolderIdentifier) : Folder
    {
        $targetFolder = null;
        if ($targetFolderIdentifier) {
            try {
                $targetFolder = ResourceFactory::getInstance()->getFolderObjectFromCombinedIdentifier($targetFolderIdentifier);
            } catch (\Exception $e) {
                $targetFolder = null;
            }
        }
        if ($targetFolder === null) {
            $targetFolder = $this->getBackendUser()->getDefaultUploadFolder();
        }
        return $targetFolder;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser() : BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
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
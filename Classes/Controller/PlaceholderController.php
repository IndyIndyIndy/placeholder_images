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

use ChristianEssl\PlaceholderImages\Service\ImageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\Exception\NotImplementedMethodException;
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

        if (!empty($width)) {
            $data = [];
            $file = $this->createFileRelation($width, $height, $format, $placeholder, $bgcolor, $textcolor, $targetFolderIdentifier);
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
     * @param integer $width
     * @param integer $height
     * @param string $format
     * @param string $placeholder
     * @param string $bgcolor
     * @param string $textcolor
     * @param string $targetFolderIdentifier
     *
     * @return File|null
     */
    protected function createFileRelation($width, $height, $format, $placeholder, $bgcolor, $textcolor, $targetFolderIdentifier)
    {
        $targetFolder = $this->getTargetFolder($targetFolderIdentifier);

        $imageService = GeneralUtility::makeInstance(ImageService::class);
        $image = $imageService->getPlaceholderImage($width, $height, $format, $placeholder, $bgcolor, $textcolor);

        // @todo support both placeholder.com and locally downloadingg the images
        throw new NotImplementedMethodException();
        return null;
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
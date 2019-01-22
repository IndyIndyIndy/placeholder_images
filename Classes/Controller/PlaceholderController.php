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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * Handles generating and uploading placeholder images
 */
class PlaceholderController
{
    /**
     * AJAX endpoint for storing the URL as a sys_file record
     *
     * @var ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createAction(ServerRequestInterface $request): ResponseInterface
    {
        //@todo do image / file relation stuff
        return new JsonResponse(['NOT IMPLEMENTED']);
    }

}
<?php

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

/**
 * Definitions for routes provided by EXT:placeholder_images
 */
return [
    'placeholderimages_create' => [
        'path' => '/placeholderimages/create',
        'target' => \ChristianEssl\PlaceholderImages\Controller\PlaceholderController::class . '::createAction'
    ]
];
<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "placeholder_images"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Placeholder Images',
    'description' => 'Easily add placeholder images in TYPO3 records generated via imagemagick.',
    'category' => 'misc',
    'author' => 'Christian Eßl',
    'author_email' => 'indy.essl@gmail.com',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

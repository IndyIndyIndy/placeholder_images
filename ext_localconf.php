<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extKey) {

        if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineControlContainer::class] = array(
                'className' => \ChristianEssl\PlaceholderImages\Xclass\InlineControlContainerVersion9::class
            );
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineControlContainer::class] = array(
                'className' => \ChristianEssl\PlaceholderImages\Xclass\InlineControlContainerVersion8::class
            );
        }

    },
    $_EXTKEY
);
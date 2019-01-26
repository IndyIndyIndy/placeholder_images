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

define(["jquery", "TYPO3/CMS/Core/Contrib/jquery.minicolors"], function($, minicolors) {
    "use strict";

    var ModalValidation = {

    };

    ModalValidation.init = function () {
        $('.t3js-color-picker').minicolors({
                format: 'hex',
                position: 'bottom left',
                theme: 'bootstrap',
        });

        $('.t3js-number').on('keyup paste', function() {
            var val = parseInt($(this).val());
            var min = parseInt($(this).attr('min'));
            var max = parseInt($(this).attr('max'));

            if (val > max) {
                $(this).val(max);
            }

            if (val < min) {
                $(this).val(min);
            }
        });
    };

    return ModalValidation;
});
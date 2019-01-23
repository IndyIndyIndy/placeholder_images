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

define(["require", "exports", "TYPO3/CMS/Backend/Enum/KeyTypes", "jquery", "nprogress", "TYPO3/CMS/Backend/Modal", "TYPO3/CMS/Backend/Severity", "TYPO3/CMS/Core/SecurityUtility"],
    function(e, t, KeyTypesEnum, $, NProgress, Modal, Severity, SecurityUtility) {
    "use strict";

    var PlaceholderImages = {
        securityUtility: new SecurityUtility()
    };

    PlaceholderImages.init = function() {
        var self = this;

        $(document).on('click', '.t3js-placeholder-add-btn', function (e) {
            self.triggerModal($(e.currentTarget));
        });
    };

    /**
     * @param {Object} $target
     */
    PlaceholderImages.triggerModal = function($target) {
        var self = this;

        var btnSubmit = $target.data('btn-submit');
        var testText = $target.data('online-media-allowed-help-text') || 'missing text3'; // @todo wrong text

        var $markup = $('<div>')
            .attr('class', 'form-control-wrap')
            .append([
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control width')
                    .attr('value', $target.data('width-default'))
                    .attr('placeholder', $target.data('width-text')),
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control height')
                    .attr('value', $target.data('height-default'))
                    .attr('placeholder', $target.data('height-text')),
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control format')
                    .attr('value', $target.data('format-default'))
                    .attr('placeholder', $target.data('format-text')),
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control placeholder')
                    .attr('value', $target.data('placeholder-default'))
                    .attr('placeholder', $target.data('placeholder-text')),
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control bgcolor')
                    .attr('value', $target.data('bgcolor-default'))
                    .attr('placeholder', $target.data('bgcolor-text')),
                $('<input>')
                    .attr('type', 'text')
                    .attr('class', 'form-control textcolor')
                    .attr('value', $target.data('textcolor-default'))
                    .attr('placeholder', $target.data('textcolor-text')),
                $('<div>')
                    .attr('class', 'help-block')
                    .html(self.securityUtility.encodeHtml(testText, false))
            ]);
        var $modal = Modal.show(
            $target.attr('title'),
            $markup,
            Severity.notice,
            [{
                text: btnSubmit,
                btnClass: 'btn btn-primary',
                name: 'ok',
                trigger: function() {
                    var width = $modal.find('input.width').val();
                    var height = $modal.find('input.height').val();
                    var format = $modal.find('input.format').val();
                    var placeholder = $modal.find('input.placeholder').val();
                    var bgcolor = $modal.find('input.bgcolor').val();
                    var textcolor = $modal.find('input.textcolor').val();

                    if (width || height) {
                        $modal.modal('hide');
                        self.addPlaceholderImage($target, width, height, format, placeholder, bgcolor, textcolor);
                    }
                }
            }]
        );
    };

    /**
     * @param {Object} $trigger
     * @param {int} width
     * @param {int} height
     * @param {string} format
     * @param {string} placeholder
     * @param {string} bgcolor
     * @param {string} textcolor
     */
    PlaceholderImages.addPlaceholderImage = function($trigger, width, height, format, placeholder, bgcolor, textcolor) {
        var target = $trigger.data('target-folder');
        var irreObjectUid = $trigger.data('file-irre-object');

        NProgress.start();
        $.post(
            TYPO3.settings.ajaxUrls.placeholderimages_create,
            {
                width: width,
                height: height,
                format: format,
                placeholder: placeholder,
                bgcolor: bgcolor,
                textcolor: textcolor,
                targetFolder: target
            }, function(data) {
                if (data.file) {
                    console.log(data);
                    console.log(irreObjectUid);
                    // @todo test this
                    window.inline.delayedImportElement(irreObjectUid, 'sys_file', data.file, 'file');
                } else {
                    var $confirm = Modal.confirm(
                        'ERROR',
                        data.error,
                        Severity.error,
                        [{
                            text: TYPO3.lang['button.ok'] || 'OK',
                            btnClass: 'btn-' + Severity.getCssClass(Severity.error),
                            name: 'ok',
                            active: true
                        }]
                    ).on('confirm.button.ok', function() {
                        $confirm.modal('hide');
                    });
                }
                NProgress.done();
            }
        );
    };

    PlaceholderImages.init();
    return PlaceholderImages;
});
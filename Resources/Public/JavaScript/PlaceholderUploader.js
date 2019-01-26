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

define(["jquery", "nprogress", "TYPO3/CMS/Backend/Modal", "TYPO3/CMS/Backend/Severity", "TYPO3/CMS/PlaceholderImages/FormBuilder"],
    function($, NProgress, Modal, Severity, FormBuilder) {
    "use strict";

        var PlaceholderUploader = {
            formBuilder: FormBuilder
        };
        var self = PlaceholderUploader;

        PlaceholderUploader.init = function() {
            $(document).on('click', '.t3js-placeholder-add-btn', function (e) {
                self.triggerModal($(e.currentTarget));
            });
        };

        /**
         * @param {Object} $target
         */
        PlaceholderUploader.triggerModal = function($target) {
            var $form = self.formBuilder.getFormMarkup($target);
            var $modal = Modal.show(
                $target.attr('title'),
                $form,
                Severity.notice,
                [{
                    text: $target.data('btn-submit'),
                    btnClass: 'btn btn-primary',
                    name: 'ok',
                    trigger: function() {
                        var width = $modal.find('input.width').val();
                        var height = $modal.find('input.height').val();
                        var format = $modal.find('select.format').val();
                        var placeholder = $modal.find('input.placeholder').val();
                        var bgcolor = $modal.find('input.bgcolor').val();
                        var textcolor = $modal.find('input.textcolor').val();
                        var count = $modal.find('input.count').val();

                        if (width || height) {
                            $modal.modal('hide');
                            self.addPlaceholderImage($target, width, height, format, placeholder, bgcolor, textcolor, count);
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
         * @param {int} count
         */
        PlaceholderUploader.addPlaceholderImage = function($trigger, width, height, format, placeholder, bgcolor, textcolor, count) {
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
                        for (var i = 0; i < count; i++) {
                            window.inline.delayedImportElement(irreObjectUid, 'sys_file', data.file, 'file');
                        }
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

        return PlaceholderUploader;
});
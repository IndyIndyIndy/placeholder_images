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
        var $markup = PlaceholderImages.getFormMarkup($target);

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
     * @param {Object} $target
     */
    PlaceholderImages.getFormMarkup = function($target) {
        var self = this;
        var testText = $target.data('online-media-allowed-help-text') || 'missing text3'; // @todo wrong text

        return $('<div>')
            .attr('class', 'form-control-wrap')
            .append([
                $('<div>')
                    .attr('class', 'row')
                    .append([
                        $('<div>')
                            .attr('class', 'form-group col-sm-6')
                            .append([
                                PlaceholderImages.getLabel('width', $target),
                                PlaceholderImages.getInput('width', $target)
                            ]),
                        $('<div>')
                            .attr('class', 'form-group col-sm-6')
                            .append([
                                PlaceholderImages.getLabel('height', $target),
                                PlaceholderImages.getInput('height', $target)
                            ]),
                    ]),
                $('<div>')
                    .attr('class', 'row')
                    .append([
                        $('<div>')
                            .attr('class', 'form-group col-sm-6')
                            .append([
                                PlaceholderImages.getLabel('bgcolor', $target),
                                PlaceholderImages.getInput('bgcolor', $target)
                            ]),
                        $('<div>')
                            .attr('class', 'form-group col-sm-6')
                            .append([
                                PlaceholderImages.getLabel('textcolor', $target),
                                PlaceholderImages.getInput('textcolor', $target)
                            ]),
                    ]),
                $('<div>')
                    .attr('class', 'row')
                    .append([
                        $('<div>')
                            .attr('class', 'form-group col-sm-6')
                            .append([
                                PlaceholderImages.getLabel('format', $target),
                                PlaceholderImages.getInput('format', $target)
                            ])
                    ]),
                $('<div>')
                    .attr('class', 'row')
                    .append([
                        $('<div>')
                            .attr('class', 'form-group col-sm-12')
                            .append([
                                PlaceholderImages.getLabel('placeholder', $target),
                                PlaceholderImages.getInput('placeholder', $target)
                            ])
                    ]),
                $('<div>')
                    .attr('class', 'help-block')
                    .html(self.securityUtility.encodeHtml(testText, false))
            ]);
    };

    /**
     * @param {string} name
     * @param {Object} $target
     */
    PlaceholderImages.getLabel = function(name, $target) {
        return $('<label>').html($target.data(name + '-text'))
    };

    /**
     * @param {string} name
     * @param {Object} $target
     */
    PlaceholderImages.getInput = function(name, $target) {
        return $('<input>')
            .attr('type', 'text')
            .attr('class', 'form-control ' + name)
            .attr('value', $target.data(name + '-default'))
            .attr('placeholder', $target.data(name + '-text'))
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
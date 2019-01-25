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

define(["jquery", "TYPO3/CMS/Core/SecurityUtility"],
    function($, SecurityUtility) {
        "use strict";

        var PlaceholderFormBuilder = {
            securityUtility: new SecurityUtility()
        };

        /**
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getFormMarkup = function($target) {
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
                                    self.getLabel('width', $target),
                                    self.getInput('width', $target)
                                ]),
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('height', $target),
                                    self.getInput('height', $target)
                                ]),
                        ]),
                    $('<div>')
                        .attr('class', 'row')
                        .append([
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('bgcolor', $target),
                                    self.getColorPicker('bgcolor', $target)
                                ]),
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('textcolor', $target),
                                    self.getColorPicker('textcolor', $target)
                                ]),
                        ]),
                    $('<div>')
                        .attr('class', 'row')
                        .append([
                            $('<div>')
                                .attr('class', 'form-group col-sm-12')
                                .append([
                                    self.getLabel('placeholder', $target),
                                    self.getInput('placeholder', $target)
                                ])
                        ]),
                    $('<div>')
                        .attr('class', 'row')
                        .append([
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('format', $target),
                                    self.getFormatSelect('format', $target)
                                ]),
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('count', $target),
                                    self.getInput('count', $target)
                                ])
                        ]),
                    $('<div>')
                        .attr('class', 'help-block')
                        .html(self.securityUtility.encodeHtml(testText, false)),
                    $('<script>' +
                        'requirejs(["TYPO3/CMS/Core/Contrib/jquery.minicolors"], function(minicolors) {\n' +
                            '\$(".t3js-color-picker").minicolors({});\n' +
                            '});' +
                        '</script>')
                ]);
        };

        /**
         * @param {string} name
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getLabel = function(name, $target) {
            return $('<label>').html($target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getInput = function(name, $target) {
            return $('<input>')
                .attr('type', 'text')
                .attr('class', 'form-control ' + name)
                .attr('value', $target.data(name + '-default'))
                .attr('placeholder', $target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getColorPicker = function(name, $target) {
            return $('<input>')
                .attr('type', 'text')
                .attr('class', 'form-control hasDefaultValue t3js-clearable t3js-color-picker formengine-colorpickerelement')
                .attr('value', $target.data(name + '-default'))
                .attr('placeholder', $target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getFormatSelect = function(name, $target) {
            var self = this;
            var defaultValue = $target.data(name + '-default');

            return $('<select>')
                .attr('class', 'form-control ' + name)
                .append([
                    self.getOption('png', defaultValue),
                    self.getOption('jpg', defaultValue),
                    self.getOption('gif', defaultValue),
                ]);
        };

        /**
         * @param {string} value
         * @param {string} defaultValue
         */
        PlaceholderFormBuilder.getOption = function(value, defaultValue) {
            var selected = (value === defaultValue);
            return '<option value"'+value+'" ' + selected + '>'+value+'</option>';
        };

        return PlaceholderFormBuilder;
});
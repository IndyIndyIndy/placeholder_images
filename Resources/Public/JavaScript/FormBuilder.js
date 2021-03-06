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

define(["jquery"],
    function($) {
        "use strict";

        var FormBuilder = {

        };
        var self = FormBuilder;

        /**
         * @param {Object} $target
         */
        FormBuilder.getFormMarkup = function($target) {
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
                                    self.getNumberInput('width', $target, 9999)
                                ]),
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('height', $target),
                                    self.getNumberInput('height', $target, 9999)
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
                                    self.getTextInput('placeholder', $target)
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
                                    self.getNumberInput('count', $target, 10)
                                ])
                        ]),
                    $(  '<script>' +
                        '   requirejs(["TYPO3/CMS/PlaceholderImages/ModalValidation"], function(ModalValidation) {' +
                        '       ModalValidation.init();' +
                        '   });' +
                        '</script>')
                ]);
        };

        /**
         * @param {string} name
         * @param {Object} $target
         *
         * @return Object
         */
        FormBuilder.getLabel = function(name, $target) {
            return $('<label>').html($target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         *
         * @return Object
         */
        FormBuilder.getTextInput = function(name, $target) {
            return $('<input>')
                .attr('type', 'text')
                .attr('class', 'form-control ' + name)
                .attr('value', $target.data(name + '-default'))
                .attr('placeholder', $target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         * @param {int} max
         *
         * @return Object
         */
        FormBuilder.getNumberInput = function(name, $target, max) {
            return $('<input>')
                .attr('type', 'number')
                .attr('min', 1)
                .attr('max', max)
                .attr('class', 'form-control t3js-number ' + name)
                .attr('value', $target.data(name + '-default'))
                .attr('placeholder', $target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         *
         * @return Object
         */
        FormBuilder.getColorPicker = function(name, $target) {
            return $('<input>')
                .attr('type', 'text')
                .attr('class', 'form-control t3js-color-picker formengine-colorpickerelement ' + name)
                .attr('value', $target.data(name + '-default'))
                .attr('placeholder', $target.data(name + '-text'));
        };

        /**
         * @param {string} name
         * @param {Object} $target
         *
         * @return Object
         */
        FormBuilder.getFormatSelect = function(name, $target) {
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
         *
         * @return string
         */
        FormBuilder.getOption = function(value, defaultValue) {
            var selected = (value === defaultValue);
            return '<option value"'+value+'" ' + selected + '>'+value+'</option>';
        };

        return FormBuilder;
});
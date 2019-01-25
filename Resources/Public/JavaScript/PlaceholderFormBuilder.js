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
                                    self.getInput('bgcolor', $target)
                                ]),
                            $('<div>')
                                .attr('class', 'form-group col-sm-6')
                                .append([
                                    self.getLabel('textcolor', $target),
                                    self.getInput('textcolor', $target)
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
                                    self.getInput('format', $target)
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
                        .html(self.securityUtility.encodeHtml(testText, false))
                ]);
        };

        /**
         * @param {string} name
         * @param {Object} $target
         */
        PlaceholderFormBuilder.getLabel = function(name, $target) {
            return $('<label>').html($target.data(name + '-text'))
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
                .attr('placeholder', $target.data(name + '-text'))
        };

        return PlaceholderFormBuilder;
});
// Field slider range
// © Denis Ineshin, 2019
//
// Project page:    http://ionden.com/a/plugins/ion.rangeSlider/en.html
// GitHub page:     https://github.com/IonDen/ion.rangeSlider
//
// Released under MIT licence:
// http://ionden.com/a/plugins/licence-en.html
// =====================================================================================================================

;(function(factory) {
    if ((typeof jQuery === 'undefined' || !jQuery) && typeof define === "function" && define.amd) {
        define(["jquery"], function (jQuery) {
            return factory(jQuery, document, window, navigator);
        });
    } else if ((typeof jQuery === 'undefined' || !jQuery) && typeof exports === "object") {
        factory(require("jquery"), document, window, navigator);
    } else {
        factory(jQuery, document, window, navigator);
    }
} (function ($, document, window, navigator, undefined) {
    "use strict";

    // =================================================================================================================
    // Service

    var plugin_count = 0;

    if (!Function.prototype.bind) {
        Function.prototype.bind = function bind(that) {

            var target = this;
            var slice = [].slice;

            if (typeof target != "function") {
                throw new TypeError();
            }

            var args = slice.call(arguments, 1),
                bound = function () {

                    if (this instanceof bound) {

                        var F = function(){};
                        F.prototype = target.prototype;
                        var self = new F();

                        var result = target.apply(
                            self,
                            args.concat(slice.call(arguments))
                        );
                        if (Object(result) === result) {
                            return result;
                        }
                        return self;

                    } else {

                        return target.apply(
                            that,
                            args.concat(slice.call(arguments))
                        );

                    }

                };

            return bound;
        };
    }
    if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(searchElement, fromIndex) {
            var k;
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            var O = Object(this);
            var len = O.length >>> 0;
            if (len === 0) {
                return -1;
            }
            var n = +fromIndex || 0;
            if (Math.abs(n) === Infinity) {
                n = 0;
            }
            if (n >= len) {
                return -1;
            }
            k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
            while (k < len) {
                if (k in O && O[k] === searchElement) {
                    return k;
                }
                k++;
            }
            return -1;
        };
    }



    // =================================================================================================================
    // Template


    // =================================================================================================================
    // Core

    /**
     * Main plugin constructor
     *
     * @param input {Object} link to base input element
     * @param options {Object} slider config
     * @param plugin_count {Number}
     * @constructor
     */
    var FieldSliderRange = function (input, options, plugin_count) {
        this.VERSION = "1";
        this.input = input;
        this.plugin_count = plugin_count;
        this.value_min = '';
        this.value_max = '';
        this.infinity = false;
        this.plugin_count = plugin_count;
        this.step = 1;
        this.self = $(input) ;
        
        this.is_key = false;
        this.is_update = false;
        this.is_start = true;
        this.is_finish = false;
        this.is_active = false;
        this.is_resize = false;
        this.is_click = false;
        var conf_data = this.self.find('.config-range') || '';
        var config;
        if(conf_data)
            var config = {
                'min' : conf_data.attr('data-min') || '',
                'max' : conf_data.attr('data-max') || '',
                'sufix' : conf_data.attr('data-sufix') || '',
                'prefix' : conf_data.attr('data-prefix') || '',
                'infinity' : conf_data.attr('data-infinity') || 'false',
                'predifinedMin' : conf_data.attr('data-predifinedMin') || '',
                'predifinedMax' : conf_data.attr('data-predifinedMax') || '',
                'onChange' : ''
                
            }
        options = options || {};

        // js config extends default config
        $.extend(config, options);

        this.options = config;

        // validate config, to be sure that all data types are correct
        this.update_check = {};

        this.init();
    };

    FieldSliderRange.prototype = {
        /**
         * Starts or updates the plugin instance
         *
         * @param [is_update] {boolean}
         */
        init: function (is_update) {
            var that = this;
            
            if(that.options.predifinedMax == '')
                that.options.predifinedMax=that.options.max;

            if(that.options.max.length > 6) {
                this.step = 10000;
            }else if(that.options.max.length > 4) {
                this.step = 100;
            }else if(that.options.max.length > 3) {
                this.step = 10;
            }
            
            this.self.find('.wdk-slider-range-input').ionRangeSlider({
                skin: "round",
                type: "double",
                grid: true,
                min: that.options.min,
                max: that.options.max,
                from: that.options.predifinedMin,
                to: that.options.predifinedMax,
                prefix: that.options.prefix,
                postfix: that.options.sufix,
                max_postfix: "+",
                step: this.step,
                decorate_both: true,
                values_separator: '-',
                onChange: function (data) {
                    that.options.onChange;
                    that.self.find('.value-min').val(data.from);
                    that.self.find('.value-max').val(data.to);
                    if(data.max==data.to)
                        that.self.find('.value-max').val('');
                    
                    if(data.min==data.from)
                        that.self.find('.value-min').val('');
                },
            });
            
        },

        /**
         * Remove slider instance
         * and unbind all events
         */
        remove: function () {
            this.remove();
        }

    };

    $.fn.fieldSliderRange = function (options) {
        return this.each(function() {
            if (!$.data(this, "fieldSliderRange")) {
                $.data(this, "fieldSliderRange", new FieldSliderRange(this, options, plugin_count++));
            }
        });
    };

}));

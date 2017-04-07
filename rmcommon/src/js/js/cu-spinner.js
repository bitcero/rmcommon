/*!
 XOOPS Spinner Plugin
 Author: Eduardo Cortés (AKA bitcero)
 Copyright: © 2015 The Xoops Project (http://xoops.org)
 License: GPL 2 or later
 */
(function ($) {

    $.fn.cuSpinner = function (options) {

        /**
         * icon can be any XOOPS SVG icon or other
         * type must be spin or pulse
         * spinner can be show or hide
         */
        var settings = $.extend({
            icon: 'svg-rmcommon-spinner-06',
            type: 'spin',
            hide: 'cu-icon',
            steps: 12, // Only useful when type equal to 'pulse'
            speed: 3    // 1 to 5 - only valid when type equal to spin
        }, options);

        settings.type = settings.type != 'spin' && settings.type != 'pulse' ? 'spin' : settings.type;

        var style = settings.type == 'pulse' ? 'animation: cu-spin 1s infinite steps('+settings.steps+')' : 'animation: cu-spin '+((2/6) * settings.speed)+'s infinite linear;'

        /**
         * Container must have a child with class xo-icon-sv
         * other wise this plugin could cause conflicts
         */
        return this.each(function () {

            var el = $(this);
            var theIcon = cuHandler.getIcon(settings.icon);

            if (!theIcon) {
                theIcon = cuHandler.getIcon('svg-rmcommon-spinner-06');
            }

            /**
             * Verify if spinner is pressent. If yes we need to remove it
             * and show the other icon
             */
            var exists = $(this).find('.cu-spinner');
            if (exists.length > 0) {
                $(exists).remove();
                $(this).find('.cu-spinner-hide').removeClass('cu-spinner-hide').show();
                return true;
            }

            /**
             * Add the spinner
             */
            var spinner = $("<span />", {class: 'cu-icon cu-spinner'});
            spinner.load(theIcon, function () {
                // Hide the required class
                var toHide = $(el).find('.' + settings.hide);
                if(toHide.length > 0){
                    toHide.addClass('cu-spinner-hide').hide();
                    $(toHide[0]).after(spinner);
                } else {
                    $(el).prepend(spinner);
                }
            });

            return true;

        });

    }

}(jQuery));
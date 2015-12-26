/*!
Xoops Preload Plugin
Shows a preload animation
 */
(function($){

    $.fn.cuPreload = function(options){

        var settings = $.extend({
            action: 'show',
            position: 'absolute'
        }, options);

        return this.each(function(){

            if( settings.action == 'hide' ){
                $(this).find(".cu-preload-animation").remove();
                $(this).find(".cu-preload-overlay").remove();
                return true;
            }

            if($(this).find(".cu-preload-overlay").length > 0){
                $(this).find(".cu-preload-overlay").css('position', settings.position);
                $(this).find(".cu-preload-animation").css('position', settings.position);
                $(this).find(".cu-preload-overlay").fadeIn(250);
                $(this).find(".cu-preload-animation").fadeIn(250);
                return true;
            }

            var html = '<div class="cu-preload-overlay" style="position: '+settings.position+';"></div><div class="cu-preload-animation" style="position: '+settings.position+';"><span id="cu-preload-animation_1"></span><span id="cu-preload-animation_2"></span><span id="cu-preload-animation_3"></span></div>';
            var currentPos = $(this).css('position');
            if ( currentPos != 'absolute' && currentPos != 'relative' && currentPos != 'fixed' )
                $(this).css('position', 'relative');
            $(this).append(html);
            $(this).find(".cu-preload-overlay").fadeIn(250, function(){
                $(this).siblings('.cu-preload-animation').fadeIn(250);
            })

        });

    }

}(jQuery));
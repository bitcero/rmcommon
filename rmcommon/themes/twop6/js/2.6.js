/*
Theme name: Two Point Six
Theme URI: http://www.redmexico.com.mx
Version: 1.0
Author: bitcero
Author URI: http://www.bitcero.info
*/

$(document).ready(function(){

    var bc_on = false;

    $(".tooltip").each(function(){
        $(this).removeClass("tooltip");
    });
    
    $("[rel='tooltip']").tooltip({
        animation: true,
        placement: 'bottom'
    });
    
    $("input[type='button']").addClass('btn');
    $("input[type='submit']").addClass('btn').addClass('btn-primary');
    
    $("table.outer").addClass("table").addClass("table-hover").addClass('table-bordered');

    $("#xo-contents").css('min-height', $(window).height()-290+'px');

    // Modules chooser
    $("#xo-showmodules").click(function(){
        var html = '<div id="xo-chooser-blocker"></div>';
        html += '<div id="xo-chooser">';
        html += '<div class="chooser-title cu-titlebar"><button type="button" class="close">&times;</button>'+$(this).data("title")+'</div>';
        html += '<div class="chooser-modules xo-loader"></div>';
        html += '</div>';
        
        $('body').append(html);
        
        $("#xo-chooser-blocker").fadeIn('fast', function(){
            $("#xo-chooser").fadeIn('fast', function(){
                
                $.get(twop6_url+"/ajax/modules.php", function(data){
                    $("#xo-chooser .chooser-modules").html(data).removeClass("xo-loader");
                },'html');
                
            });
        })
        .click(function(){
            $("#xo-chooser").fadeOut('fast', function(){
                $("#xo-chooser-blocker").fadeOut('fast');
            });
        });
        
        $("#xo-chooser .close").click(function(){
            $("#xo-chooser-blocker").click();
        });
        
    });

    $("body").on("click", '.cu-box .box-handler', function(){
        $(this).parent().parent().find(".box-content").slideToggle('fast');

        if($(this).hasClass("fa-caret-down"))
            $(this).removeClass("fa-caret-down").addClass('fa-caret-up');
        else
            $(this).removeClass("fa-caret-up").addClass('fa-caret-down');

    });

    $("body").on('click', ".rm_help_button, .cu-help-button", function(){

        /*$.window({
            title: $(this).attr('title'),
            headerClass: "th bg_dark",
            url: $(this).attr('href'),
            width: '40%',
            minWidth: '300px',
            height: 500,
            resizable: true,
            maximizable: true,
            minimizable: false,
            y: 10,
            x: '60%'
        });*/

        $("#xo-context-help > .title").html('<span class="fa fa-question-circle"></span> ' + $(this).attr('title'));

        if ( $("#xo-context-help > iframe").length > 0 )
            $("#xo-context-help iframe").attr("src", $(this).attr('href') );
        else
            $("#xo-context-help").append('<iframe src="' + $(this).attr('href') + '"></iframe>');

        $("#xo-context-help .help-switch").removeClass('fa-question-circle').addClass('fa-angle-double-right');

        $("body").addClass('xo-help');

        return false;

    });

    $("#xo-context-help .help-switch").click( function(){

        if ( $("body").hasClass('xo-help') ) {
            $("body").removeClass('xo-help').addClass('xo-without-help');
            $(this).removeClass('fa-angle-double-right').addClass('fa-question-circle');
        } else {
            $("body").removeClass('xo-without-help').addClass('xo-help');
            $(this).removeClass('fa-question-circle').addClass('fa-angle-double-right');
        }

    });

    $("#xo-context-help .help-close").click( function(){
        $("body").removeClass('xo-help').removeClass('xo-without-help');
        $("#xo-context-help iframe").remove();
        $("#xo-context-help .title").html('');
    });

    $(".cu-breadcrumb-container > .breadcrumb-puller").click(function(){

        bc_on = !bc_on;
        var ele = $(this);

        if ( !bc_on ){
            $(".cu-breadcrumb-container").animate({
                top: '0px'
            }, 100, function(){
                $(ele).addClass("showing");
            });

            return true;
        } else {
            $(".cu-breadcrumb-container").animate({
                top: '-32px'
            }, 100, function(){
                $(ele).removeClass("showing");
            });
        }

        return false;

    });

    $(".twop6-scheme").click(function(){

        var file = $(this).data('file');
        if (file==undefined || file=='')
            return;

        var url = xoUrl + '/modules/rmcommon/themes/twop6/css/schemes/' + file;
        $("#color-scheme").attr("href", url);

        $.cookie('color_scheme', file, { expires: 365, path: '/' });

        $(".twop6-scheme span.fa").remove();
        $(".twop6-scheme[data-file='" + $(this).data("file") + "']").prepend('<span class="fa fa-check"></span>');

    });
    
});

function updatesNotifier(count){
    if(count<=0) return;
    $("#xo-menubar .xo-upd-notifier").html($("#xo-menubar .xo-upd-notifier").html().replace("%s", count));
    $("#xo-menubar .xo-upd-notifier").fadeIn('fast');
}
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
        html += '<div class="chooser-title xo-bluebar"><button type="button" class="close">&times;</button>'+$(this).attr("title")+'</div>';
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

    $(".cu-box .box-handler").click(function(){
        $(this).parent().parent().children(".box-content").slideToggle('fast');

        if($(this).hasClass("fa-caret-down"))
            $(this).removeClass("fa-caret-down").addClass('fa-caret-up');
        else
            $(this).removeClass("fa-caret-up").addClass('fa-caret-down');

    });

    $("a.rm_help_button").click(function(){

        $.window({
            title: $(this).attr('title'),
            headerClass: "th bg_dark",
            url: $(this).attr('href'),
            width: '80%',
            minWidth: '300px',
            height: 500,
            resizable: true,
            maximizable: true,
            minimizable: false,
            y: 10,
            x: $(window).width()-510,
        });

        return false;

    });

    $(".cu-breadcrumb-container > .breadcrumb-puller, .cu-breadcrumb-container > .rmc-breadcrumb").hover(function(){

        bc_on = true;

        if ( !$(".cu-breadcrumb-container").hasClass( 'displayed' ) ){
            $(".cu-breadcrumb-container").animate({
                top: '0px'
            }, 100)
                .addClass( "displayed" );

            return true;
        }

        return false;

    });

    $(".cu-breadcrumb-container > .rmc-breadcrumb").mouseleave( $.debounce( 1000, function( e ){

        bc_on = false;

        if ( $(".cu-breadcrumb-container").hasClass( 'displayed' ) && bc_on == false ){
            $(".cu-breadcrumb-container").animate({
                top: '-32px'
            }, 100)
                .removeClass( "displayed" );
        }
    } ) );
    
});

function updatesNotifier(count){
    if(count<=0) return;
    $("#xo-menubar .xo-upd-notifier").html($("#xo-menubar .xo-upd-notifier").html().replace("%s", count));
    $("#xo-menubar .xo-upd-notifier").fadeIn('fast');
}
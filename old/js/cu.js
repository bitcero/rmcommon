$(document).ready( function(){

    $('a.scroll').click( function() {

        var $link = $(this);
        var anchor  = $link.attr('href');
        $('html, body').stop().animate({
            scrollTop: $(anchor).offset().top
        }, 1000);

    });

});
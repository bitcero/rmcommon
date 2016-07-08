(function($){

    this.CUIcons = function(){

    };

    CUIcons.prototype.loadFromProvider = function(provider){

        if(undefined == provider || '' == provider){
            return false;
        }

        cuHandler.showLoader();

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            provider: provider,
            action: 'load-icons',
            size: $("#icons-sizes :radio:checked").val()
        };

        $.get('icons.php', params, function(response){

            cuHandler.closeLoader();

            if(!cuHandler.retrieveAjax(response)){

                $(".icons-container .panel-body").append('<span class="label label-warning">' + response.message + '</span>');
                return false;
            }

            $(".icons-container .panel-title").html(response.message);
            $(".icons-container .panel-body").html(response.content);

        }, 'json');

    };

    CUIcons.prototype.changeSize = function(size){

        if(undefined == size || size <= 0){
            return false;
        }

        $(".icons-container .icons-grid").attr('data-size', size);

    };

    CUIcons.prototype.showData = function(ele){

        if(undefined == ele || ele.length <= 0){
            return false;
        }

        var params = {
            icon: '<span class="icon-load"></span>',
            iconCode: $(ele).data('icon'),
            provider: $(ele).parents('.icons-grid').data('provider'),
            file: $(ele).attr("title")
        };

        var template = $.templates('#details-tpl');

        var container = $('<div/>')
            .attr('id', 'icon-details')
            .append(template.render(params));

        $(container).find('.icon-load').append(cuHandler.loadIcon($(ele).data('icon')));

        $('body').append(container);

    };
    
    CUIcons.prototype.filter = function(ele){

        var filter = $(ele).val().trim(), count = 0;

        if(filter.length < 1){
            $(".icons-grid > li").fadeIn();
            return null;
        }

        $(".icons-grid > li").each(function(){
            // If the list item does not contain the text phrase fade it out
            if ($(this).data('icon').search(new RegExp(filter, "i")) < 0) {
                $(this).fadeOut();

                // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).fadeIn();
                count++;
            }
        });
        
    };

    var icons = new CUIcons();

    $("#provider").change(function(){

        icons.loadFromProvider($(this).val());

    });

    $("#icons-sizes :radio").change(function(){

        icons.changeSize($(this).val());

    });

    $("body").on('click', '.icons-grid a', function(){

        icons.showData($(this));
        return false;

    });

    $("body").on('click', "#icon-details .close", function(){
        $("#icon-details").fadeOut(500, function(){
            $(this).remove();
        });
    });

    $("#icons-search").keyup(function(){

        icons.filter($(this));

    });

}(jQuery));
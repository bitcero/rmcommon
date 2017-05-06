(function($){

    /**
     * Capture click from search icons button
     */
    $("body").on('click', '.adv-icons-selector > .icons-button', function(){

        var $parent = $(this).parent();
        var $button = $(this);

        var params = {
            icon: $parent.find('input[type="hidden"]').val(),
            'CUTOKEN_REQUEST': $('#cu-token').val(),
            parent: $parent.attr('id')
        };

        $(this).cuSpinner({
            icon: 'svg-rmcommon-spinner-14'
        });

        $.get(xoUrl + '/modules/rmcommon/plugins/advform-pro/includes/icons-browser.php', params, function(response){

            $button.cuSpinner();

            if(!cuHandler.retrieveAjax(response, false)){
                cuHandler.notify({
                    text: response.message,
                    type: 'alert-danger',
                    icon: 'svg-rmcommon-error'
                });
                return false;
            }

        }, 'json');

    });

    /**
     * Clear field
     */
    $("body").on('click', '.adv-icons-selector > .clear-button', function(){

        var $parent = $(this).parent();

        $parent.find('input[type="hidden"]').val('');
        $parent.find('.icons-caption').html($parent.attr("placeholder"));
        $parent.find('.icons-icon').html('');

    });

    $('body').on('show.bs.tab', ".adv-icons-browser a[data-toggle='tab']", function(){
        $("#adv-control-search").val('');
    });

    /**
     * Search for icons
     */
    $("body").on('keyup', '#adv-control-search', function() {

        var filter = $(this).val().trim(), count = 0;
        var tab = '#' + $(".adv-icons-browser .tab-content > .active").attr("id");

        if(filter.length < 3){
            $(tab + " li").fadeIn();
            return null;
        }

        $(tab + " a").each(function(){
            // If the list item does not contain the text phrase fade it out

            if ($(this).data('icon').search(new RegExp(filter, "i")) < 0) {
                $(this).parent().fadeOut();

                // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).parent().show();
                count++;
            }
        });

    });

    // Icon Pick
    $("body").on('click', '.adv-icons-browser .adv-icons-list a', function(){
        var icon = $(this).data('icon');
        var parent = $(this).parents('.adv-icons-browser').data('parent');
        var type = $(this).parents('ul').data('type');

        if(undefined == icon || '' == icon){
            cuHandler.notify({
                text: advLang.noIcon,
                type: 'alert-danger',
                icon: 'svg-rmcommon-error'
            });
            return false;
        }

        if(undefined == parent || '' == parent){
            cuHandler.notify({
                text: advLang.noParent,
                type: 'alert-danger',
                icon: 'svg-rmcommon-error'
            });
            return false;
        }

        parent = '#' + parent;

        if($(parent).length <= 0){
            cuHandler.notify({
                text: advLang.noParent,
                type: 'alert-danger',
                icon: 'svg-rmcommon-error'
            });
            return false;
        }

        $(parent).find('input[type="hidden"]').val(icon);
        $(parent).find('.icons-caption').html(icon);

        if(type == 'font'){
            icon = '<span class="' + icon + '"></span>';
        }

        cuHandler.loadIcon(icon, $(parent).find('.icons-icon'), true);

        $(this).parents('.modal').modal('hide');

        return false;
    });

}(jQuery));
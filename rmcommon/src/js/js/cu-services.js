(function($){

    $(".services-container :radio").change(function(){
        var provider = $(this).val();
        var service = $(this).parents('[data-service]').data('service');

        if('' == provider || undefined == service || '' == service){
            return false;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'assign-provider',
            service: service,
            provider: provider,
            name: $(this).data('name')
        };

        // Disable radios
        var inputs = $(this).parents('[data-service]').find('input:radio');
        $(inputs).disable();

        $.post('services.php', params, function(response){
            if(!cuHandler.retrieveAjax(response)){
                return false;
            }

            $(inputs).enable();


        }, 'json');
    });

}(jQuery));
(function($){

    this.StatesController = function(){}

    StatesController.prototype.clear = function(field){

        if(undefined == field){
            return null;
        }

        $(field).html('');

    }

    StatesController.prototype.loadList = function(field, countries){
        if(undefined == field){
            return null;
        }

        if(undefined == countries || countries == ''){
            return null;
        }

        var selected = {};

        if(undefined != $(field).data('selected')){
            var val = $(field).data('selected').split("|");

            for(var i=0;i<=val.length;i++){

                selected[val[i]] = null;

            }
        }

        $.get(xoUrl + '/modules/rmcommon/plugins/advform-pro/includes/states.php?country=' + countries, function(response){

            if(!cuHandler.retrieveAjax(response)){
                return;
            }

            if(response.hasgroups == 'no'){
                for(var key in response.states){
                    if (response.states.hasOwnProperty(key)) {

                        var thisOption = $('<option/>')
                            .attr('value', response.country + ':' + response.states[key].code)
                            .html(response.states[key].name);

                        if(response.states[key].code in selected){
                            thisOption.prop('selected', true);
                        }

                        $(field).append(thisOption);
                    }
                }

                $(field).trigger("chosen:updated");
                return;
            }

            for(var key in response.countries){
                if(response.countries.hasOwnProperty(key)){
                    var thisGroup = $("<optgroup/>")
                        .attr('label', response.countries[key].country);

                    for(var st in response.countries[key].list){

                        if(response.countries[key].list.hasOwnProperty(st)){
                            var thisOption = $('<option/>')
                                .attr('value', key + ':' + response.countries[key].list[st].code)
                                .html(response.countries[key].list[st].name);

                            if(response.countries[key].list[st].code in selected){
                                thisOption.prop('selected', true);
                            }

                            $(thisGroup).append(thisOption);
                        }

                    }

                    $(field).append(thisGroup);
                }
            }

            $(field).trigger("chosen:updated");


        }, 'json');
    }

    $(document).ready(function(){

        /**
         * Load states list if states field is present and
         * a country has been assigned to it
         */
        var statesFields = $("[data-advf-field='adv-states']");
        if(statesFields.length > 0){

            $(statesFields).each(function(){
                var country = $(this).data('country');
                if(undefined == country){
                    return;
                }

                var controller = new StatesController();
                controller.loadList($(this), country);

            });

        }

        /**
         * Update states field when country field changes
         */
        $("body").on('change', '[data-advf-field="adv-countries"]', function(){
            var id= $(this).attr("id");
            var related = $('[data-country-field="' + id + '"]');

            if(undefined != related && related.length > 0){
                var controller = new StatesController();
                controller.clear(related);
                controller.loadList(related, $(this).val());

            }
        });

    });

})(jQuery);

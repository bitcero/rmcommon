(function($){

    this.AdminSuite = function(){

        var $thisObject = this;

        // Event listeners
        function eventListeners(){

            $("[data-toggle='window']").click(function(e){

                var $module = $(this).data('module');
                var $window = $(this).data('window');

                if(undefined == $module || '' == $module){
                    return false;
                }

                if(undefined == $window || '' == $window){
                    return false;
                }

                $(this).trigger($module + 'WindowOpen', {module:$module, window:$window});

            });

        }

        this.init = function(){

            eventListeners();

        };

        /**
         * Trigger event to open windows
         * @param $mod Module name
         * @param $win Window identifier
         */
        this.triggerWindow = function($mod, $win){

            if('object' != typeof window['productsModule']){
                return false;
            }



        }



    };

    this.adminSuite = new AdminSuite();
    adminSuite.init();

})(jQuery);
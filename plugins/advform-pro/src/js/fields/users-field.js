(function($){

    this.UsersField = function(){

        this.formatData = function(data){

            if(data.loading) return data.text;

            var markup = '<div class="adv-users-field-result clearfix">' +
                '<img src="%avatar">' +
                '<div class="user-meta">' +
                '<span class="user-name">%uname</span>' +
                '<span class="user-uid">%uid</span>' +
                '<span class="user-email">%email</span>' +
                '</div></div>';

            var name = '' != data.name ? data.name + ' (' + data.uname + ')' : data.uname;

            markup = markup.replace('%avatar', data.avatar)
                .replace('%uname', name)
                .replace('%uid', advFormLang.uid.replace('%uid', data.uid))
                .replace('%email', advFormLang.email.replace('%email', data.email));

            return markup;

        };

        this.formatSelection = function(data){
            return data.uname || data.text;
        };

    };

    $(document).ready(function(){
        var props = {};
        var usersField = new UsersField();

        props.ajax = {
            url: xoUrl + '/modules/rmcommon/plugins/advform-pro/includes/users.php',
            dataType: 'json',
            delay: 300,
            data: function(params){
                return {
                    search: params.term,
                    page: params.page,
                    CUTOKEN_REQUEST: $("#cu-token").val()
                }
            },
            processResults: function(data, params){
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            }

        };

        props.templateResult = usersField.formatData;
        props.escapeMarkup = function(markup){return markup;};
        props.templateSelection = usersField.formatSelection;

        $("select[data-advf-field='users-field']").each(function(){

            var placeholder = $(this).attr('placeholder');

            // Set properties
            if(undefined != placeholder && '' != placeholder){
                props.placeholder = placeholder;
            }

            $(this).select2(props);

        });
    });

})(jQuery);
/**
 * Groups controller for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   https://eduardocortes.mx
 * @link   https://rmcommon.bitcero.dev
 */

var groupsController = {

    /**
     * Get a single selected group
     */
    edit: function( ele ){

        var eles = $("#groups-list :radio[data-switch]:checked");

        if ( eles.length <= 0 || eles.length > 1 )
            return false;

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            id: $(eles[0]).val(),
            action: 'new-group'
        };

        $.get('groups.php', params, function(response){

            if(!cuHandler.retrieveAjax(response)){
                return false;
            }

            cuHandler.modal.dialog({
                message: response.content,
                title: response.message,
                icon: response.icon != undefined ? response.icon : '',
                width: response.width != undefined ? response.width : '',
                id: response.id,
                animate: false,
                color: response.color
            });

        }, 'json');

        return params;

    },

    delete: function( e ){

        if ( !confirm( cuLanguage.confirmDelete ) )
            return false;

        var eles = $("#groups-list :radio[data-switch]:checked");
        var ids = '';
        $(eles).each( function( index ){
            ids += ids==''?$(this).val():','+$(this).val();
        } );

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            ids: ids.split(","),
            action: 'delete-group'
        };

        cuHandler.showLoader();
        $.post( 'groups.php', params, function( response ){

            if ( !cuHandler.retrieveAjax( response ) )
                return false;

        }, 'json');

    }

};


/**
 * Groups controller for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   http://eduardocortes.mx
 * @link   http://rmcommon.com
 */

var groupsController = {

    /**
     * Get a single selected group
     */
    retrieveSingle: function( ele ){

        var eles = $("#groups-list :checkbox[data-switch]:checked");
        if ( eles.length <= 0 || eles.length > 1 )
            return false;

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            id: $(eles[0]).val()
        };

        return params;

    },

    delete: function( e ){

        if ( !confirm( cuLanguage.confirmDelete ) )
            return false;

        var eles = $("#groups-list :checkbox[data-switch]:checked");
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
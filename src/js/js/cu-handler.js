/**
 * JS utilities for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   http://eduardocortes.mx
 * @link   http://rmcommon.com
 */

var cuHandler = {

    /**
     * Propiedades
     */
    ismobile: /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()),

    /**
     * Send a request to a remote URL and present the response in a window
     * Launcher can have several useful data attributes:
     * <strong>data-url</strong>: URL where the request will be send
     * @param launcher Element that will function as launcher
     * @returns {boolean}
     */
    loadRemoteDialog: function( launcher ){

        var url = $(launcher).attr("href") != undefined && $(launcher).attr("href") != '#' ? $(launcher).attr("href") : $(launcher).data('url');
        var handler = $(launcher).data("handler");
        var window_id = $(launcher).data("window-id");
        var params_retriever = $(launcher).data('retriever');
        var params_setter = $(launcher).data('parameters');

        if ( params_retriever != undefined )
            var params = eval(params_retriever+'(launcher)');
        else if ( params_setter != undefined ){

            eval("var params = " + params_setter);

        }
        else
            var params = {CUTOKEN_REQUEST: $("#cu-token").val()};

        if (params==false)
            return false;

        cuHandler.showLoader();

        $.get(url, params, function(response){

            if(!cuHandler.retrieveAjax( response ) )
                return false;

            if (handler == undefined && handler != '' ){

                cuHandler.closeLoader();

                cuDialog.dialog({
                    message: response.content,
                    title: response.message,
                    icon: response.icon != undefined ? response.icon : '',
                    width: response.width != undefined ? response.width : '',
                    id: response.windowId != undefined ? response.windowId : window_id,
                    animate: false,
                    closeButton: response.closeButton != undefined ? response.closeButton : true
                });

            } else {

                cuHandler.closeLoader();
                eval(handler+"(response, launcher);");

            }

            $(".cu-data-table").each( function(){
                cuHandler.createDataTable( $(this) );
            });

            cuHandler.checkAjaxAction( response );

            return false;

        }, 'json');

        return false;

    },

    submitAjaxForm: function(form){

        if ( !$(form).valid() )
            return false;

        cuHandler.showLoader();

        var params = form.serialize();
        params += "&CUTOKEN_REQUEST=" + $("#cu-token").val();

        var action = form.attr("action");
        var method = form.attr("method");

        if ( method == 'post' )
            $.post( action, params, cuHandler.retrieveAjax, 'json');
        else
            $.get( action, params, cuHandler.retrieveAjax, 'json');

        return false;

    },

    // Retrieve information for AJAX-FORMS
    retrieveAjax: function(response){

        if (response.type=='error'){

            if (response.modal_message!=undefined)
                cuDialog.alert({
                    message: response.message
                });
            else
                alert(response.message);

        }

        if( response.token!='' )
            $("#cu-token").val(response.token);

        cuHandler.closeLoader();

        cuHandler.checkAjaxAction( response );

        if (response.type=='error')
            return false;

        return true;

    },

    checkAjaxAction: function( data ){

        /**
         * Ejecución de otras acciones
         */
        if ( data.showMessage != undefined )
            alert( data.message );

        // closeWindow: "#window-id"
        if(data.closeWindow != undefined)
            $(data.closeWindow).modal('hide');

        if(data.runHandler != undefined)
            eval(data.runHandler + "(data)");

        if (data.goto != undefined)
            window.location.href = data.goto;

        if(data.function != undefined)
            eval(data.function);

        if(data.openDialog != undefined){

            cuDialog.dialog({
                message: data.content,
                title: data.message,
                icon: data.icon != undefined ? data.icon : '',
                width: data.width != undefined ? data.width : '',
                owner: data.owner != undefined ? data.owner : '',
                id: data.windowId!=undefined ? data.windowId : '',
                animate: false,
                closeButton: data.closeButton != undefined ? data.closeButton : true
            });

        }

        // Reload
        if ( data.reload != undefined ){
            window.location.reload();
            return;
        }

    },

    showLoader: function(){

        $(".cu-window-loader").hide();
        $(".cu-window-blocker").hide();

        var html = '<div class="cu-window-blocker"></div>';
        html += '<div class="cu-window-loader">' +
            '<div class="loader-container text-center">' +
            '<button class="close" type="button">&times;</button>' +
            '<span>Operación en progreso...</span>' +
            '</div></div>';

        $('body').append(html);

        $(".cu-window-blocker").fadeIn(0, function(){
            $(".cu-window-loader").fadeIn(1);
        });

    },

    closeLoader: function( handler ){
        $(".cu-window-loader").fadeOut(1, function(){
            $(".cu-window-blocker").fadeOut(0, function(){
                $(".cu-window-loader").remove();
                $(".cu-window-blocker").remove();
                if (handler != 'undefined')
                    handler;
            });
        });
    },

    getURI: function( module, controller, action, zone, params ){

        var url = xoUrl;

        if( cu_modules[module] != undefined && controller != undefined && controller != '' ){

            url += zone=='backend'?'/admin':'';
            url += cu_modules[module] + '/' + controller + '/' + action + '/';

        } else {

            url += '/modules/' + module;

            url += zone=='backend'?'/admin':'';
            if ( controller == '' || controller == undefined )
                return url;

            url += '/index.php/' + controller + '/' + action + '/';

        }

        if (params == undefined)
            return url;

        var query = '';
        for(key in params){
            query += (query=='' ? '?' : '&') + key + '=' + eval('params.' + key);
        }

        return url + query;

    },

    /**
     * Crea una tabla de datos
     * @param ele
     */
    createDataTable: function( ele ){

        if ( ele.hasClass("dataTable") )
            return;

        var exclude = $(ele).data("exclude");
        var cols = exclude != undefined ? exclude.toString().split(",") : '';

        $(ele).dataTable( {
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $(ele).data('source'),
            "bPaginate": true,
            //"aoColumnDefs": [exclude != undefined ? {"bSortable": false, "aTargets": cols} : '']
        } );

    },

    /**
     * Controlar la barra de estado
     *
    showInPanel: function( panel, message, process ){

        process = process == undefined ? false : process;
        var loader = '';
        if (process)
            loader = '<img src="' + xoUrl + '/themes/smarterp/images/loader.gif">';

        $("#status-" + panel).html(loader + ' &nbsp; ' +message);

    },

    getPanel: function(panel){
        return $("#status-" + panel).html();
    },*/

    /**
     * Ejecuta una acción asociada a un elemento específico
     */
    runAction: function( e ) {

        var action = $(e).data("action");

        switch(action){
            case 'load-remote-dialog':
            case 'load-module-dialog':
                cuHandler.loadRemoteDialog( e );
                break;
            case 'goto':
                var url = $(e).data("url");
                if ( url == undefined )
                    url = $(e).attr("href");
                var retriever = $(e).data("retriever");

                if (retriever != undefined)
                    url = url + eval(retriever+'(e)');

                if(url!=undefined && url!='')
                    window.location.href = url;
                break;
            default:
                eval( action + "(e)" );
                break;
        }

    },

    enableCommands: function( id_activator, type ){

        var commands = $("*[data-activator='"+id_activator+"']");

        var total = $("#" + id_activator + " :"+type+"[data-switch]:checked").length;

        $(commands).each(function(index){

            var required = $(this).data("oncount")!=undefined ? $(this).data("oncount") : ' >= 1';

            if ( eval('total ' + required))
                $(this).enable();
            else
                $(this).disable();


        });

    }

};

/**
 * Currenty format
 */
Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

/**
 * jQuery plugin to enable or disable an element
 */
jQuery.fn.enable = function(){
    this.each(function(){
        jQuery(this).attr("disabled", false);
        jQuery(this).removeClass("disable");
    });
}

jQuery.fn.disable = function(){
    this.each(function(){
        jQuery(this).attr("disabled", true);
        jQuery(this).addClass("disable");
    });
}

$(document).ready(function(){

    var textarea_style = '';

    /**
     * Cargar diálogos de otros módulos
     */
    $('body').on('click', '*[data-action]', function(){
        cuHandler.runAction( $(this) )
        return false;

    });

    $("body").on('submit', 'data-type["ajax"]', function(){
        cuHandler.submitAjaxForm( $(this) );
        return false;
    });

    $('body').on("click", ".cu-window-loader .close", function(){
        cuHandler.closeLoader();
    });

    $(".cu-data-table").each( function(){
        cuHandler.createDataTable( $(this) );
    });

    $("*[data-rel='tooltip']").tooltip();

    /**
     * Activar comandos
     */
    $("body").on('change', '.activator-container :checkbox[data-switch], .activator-container :radio[data-switch]', function(){

        var id_container = $(this).parents(".activator-container").attr("id");

        cuHandler.enableCommands( id_container, $(this).attr("type") );

        if ( $(this).is(":checked") )
            $(this).parents("tr").addClass( 'tr-checked' );
        else
            $(this).parents("tr").removeClass( 'tr-checked' );

    });

    /**
     * Select rows
     */
    $("body").on('click', '.activator-container > tbody > tr > td', function(){

        var input = $(this).parent().find("input[data-switch]");

        if ( input.is(":checked") )
            input.removeAttr( "checked" );
        else
            input.prop( "checked", 'checked' );

        input.change();

    });

    /**
     * Select all checkbox
     */
    $("body").on("change", ":checkbox[data-checkbox]", function(){

        var checkbox_class = $(this).data("checkbox");

        if ( checkbox_class == undefined )
            return;

        $(":checkbox[data-oncheck='"+checkbox_class+"']").prop('checked', $(this).prop('checked'));

    });


    $("body").on("change", ':checkbox', function(){

        if( this.hasAttribute('data-checkbox') )
            return;

        if( !this.hasAttribute('data-oncheck') )
            return;

        var existing = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']");
        var checked = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']:checked");

        var activator = $(":checkbox[data-checkbox='" + $(this).data('oncheck') + "']");
        if ( activator.length <= 0 )
            return;

        if ( checked.length <= 0 )
            $(activator).removeAttr( 'checked' );
        else if ( checked.length > 0 && !$(activator).is(":checked") )
            $(activator).prop( 'checked', 'checked' );

    });

    /**
     * Check if there are a "News" box in current page
     * and then, load the news for this module
     */
    if ( $("*[data-news='load']").length == 1 || $("*[data-boxes='load']").length > 0 ){

        var container = $("*[data-news='load']");
        if (container.length <= 0)
            container = $("*[data-boxes='load']");

        if ( container.length <= 0 )
            return false;

        var module = container.data('module');
        var target = $(container.data('target'));

        if ( target != undefined )
            target.html( '<div class="text-success"><span class="fa fa-spinner fa-spin"></span> ' + cuLanguage.downloadNews + '</div>' );

        var bcontainer = $("*[data-boxes='load']");

        var params = {
            module: module,
            CU_TOKEN: $("#cu-token").val()
        };

        $.get( xoUrl + '/modules/rmcommon/ajax/module-info.php', params, function( response ){

            if ( response.type == 'error' ){
                target.html( '<div class="text-danger"><span class="fa fa-exclamation-triangle"></span> '+cuLanguage.downloadNewsError+'</div>')
                return;
            }

            /**
             * Get News
             */
            if ( response.news != undefined && target != undefined ){

                news = $("<ul>").addClass("cu-ajax-news list-unstyled");
                for( i=0; i < response.news.length; i++ ){

                    var html = '<li>' +
                        '<small>'+response.news[i].date+'</small>' +
                        '<h5><a href="'+response.news[i].link+'" target="_blank">'+response.news[i].title+'</a></h5>';
                    /*if ( response.news[i].image )
                        html += '<img src="'+response.news[i].image+'" class="img-responsive">';*/

                    html += '<p class="help-block">'+response.news[i].content+'</p>' +
                        '</li>';
                    news.append( html );

                }
                target.html('').append(news);
                news.fadeIn('fast');

            }

            /**
             * Get boxes
             */
            if ( response.boxes != undefined && bcontainer != undefined ){

                for ( i=0; i < response.boxes.length; i++ ){

                    var box = $("<div>").addClass('cu-box').css("display", 'none');
                    box.append('<div class="box-header"><span class="fa fa-caret-up box-handler"></span><h3>'+ response.boxes[i].title +'</h3></div>');
                    box.append('<div class="box-content">'+response.boxes[i].content+'</div>');
                    // Get the box position
                    if (response.boxes[i].container != undefined){
                        var box_container = $(response.boxes[i].container);
                        if (box_container.length > 0){
                            $(box_container).each(function(){
                                var newbox = box.clone();
                                if ( response.boxes[i].position == 'top' )
                                    $(this).prepend(newbox);
                                else
                                    $(this).append(newbox);
                                newbox.fadeIn('fast');
                            });
                        }
                    }

                }

            }


        }, 'json' );

    }

    /**
     * Editor full screen
     */
    $("body").on('click', ".ed-container .full-screen", function(){
        $(this).parents(".ed-container").addClass('full-screen-edit');
        $(this).removeClass('full-screen').addClass('normal-screen');
        textarea_style = $(this).parents(".ed-container").find('.txtarea-container').attr("style");

        $(this).parents(".ed-container").find('.txtarea-container').attr("style", '');
        $("body").css("overflow", 'hidden');

    });
    $("body").on('click', ".ed-container .normal-screen", function(){
        $(this).parents(".ed-container").removeClass('full-screen-edit');
        $(this).addClass('full-screen').removeClass('normal-screen');
        $(this).parents(".ed-container").find('.txtarea-container').attr("style", textarea_style);
        textarea_style = '';
        $("body").css("overflow", 'visible');
    });

    /**
     * Set user notifications
     */
    $(".cu-notifications form .notification-item").change( function(){

        var event = $(this).data('event');
        var status = $(this).is(":checked") ? 1 : 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post( xoUrl + '/notifications.php', params, function( response ){

            if ( 'error' == response.type ){
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .animate({
                    backgroundColor: '#ffff99'
                },500,'linear', function(){
                    $(this).animate({
                        backgroundColor: 'transparent'
                    }, 500, 'linear');
                });

        }, 'json' );

    } );

    $(" #cu-notifications .cancel-subscription").click( function() {

        var event = $(this).data('info');
        var status = 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post( xoUrl + '/notifications.php', params, function( response ){

            if ( 'error' == response.type ){
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .slideUp(250, function(){
                    $(this).remove();
                });

        }, 'json' );

    } );

});


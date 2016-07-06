/**
 * JS utilities for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   http://eduardocortes.mx
 * @link   http://rmcommon.com
 */

//@prepros-prepend 'bootbox.js';
//@prepros-prepend 'cu-spinner.js';
//@prepros-prepend 'pnotify.custom.js';
//@prepros-prepend 'jsrender.min.js';
//@prepros-append 'rmtable.js';

var cuHandler = {

    /**
     * Propiedades
     */
    ismobile: /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()),

    currentResponse: false,

    /**
     * Send a request to a remote URL and present the response in a window
     * Launcher can have several useful data attributes:
     * <strong>data-url</strong>: URL where the request will be send
     * @param launcher Element that will function as launcher
     * @returns {boolean}
     */
    loadRemoteDialog: function (launcher) {

        var url = $(launcher).attr("href") != undefined && $(launcher).attr("href") != '#' ? $(launcher).attr("href") : $(launcher).data('url');
        var handler = $(launcher).data("handler");
        var window_id = $(launcher).data("window-id");

        var params = {CUTOKEN_REQUEST: $("#cu-token").val()};

        if (params == false)
            return false;

        cuHandler.showLoader();

        $.get(url, params, function (response) {

            if (!cuHandler.retrieveAjax(response))
                return false;

            if (handler == undefined && handler != '') {

                cuHandler.closeLoader();

                cuHandler.modal.dialog({
                    message: response.content,
                    title: response.message,
                    icon: response.icon != undefined ? response.icon : '',
                    width: response.width != undefined ? response.width : '',
                    id: response.id != undefined ? response.id : window_id,
                    animate: false,
                    color: response.color != undefined ? response.color : '',
                    closeButton: response.closeButton != undefined ? response.closeButton : true,
                    solid: response.solid != undefined ? true: false
                });

            } else {

                cuHandler.closeLoader();
                eval(handler + "(response, launcher);");

            }

            $(".cu-data-table").each(function () {
                cuHandler.createDataTable($(this));
            });

            cuHandler.checkAjaxAction(response);

            return false;

        }, 'json');

        return false;

    },

    submitAjaxForm: function (form) {

        if (!$(form).valid())
            return false;

        cuHandler.showLoader();

        var params = form.serialize();
        params += "&CUTOKEN_REQUEST=" + $("#cu-token").val();

        var action = form.attr("action");
        var method = form.attr("method");

        if (method == 'post')
            $.post(action, params, cuHandler.retrieveAjax, 'json');
        else
            $.get(action, params, cuHandler.retrieveAjax, 'json');

        return false;

    },

    /**
     * Send an AJAX request
     * @param e DOM element
     * @param data
     */
    requestAjax: function (e, data) {

        $(e).cuSpinner({icon: 'svg-rmcommon-spinner-14'});

        if (data == undefined) {
            return false;
        }

        if (data.url == undefined) {
            return false;
        }

        if (data.parameters == undefined) {
            data.parameters = {};
        }

        data.parameters.CUTOKEN_REQUEST = $("#cu-token").val();

        if (data.method == undefined || data.method == 'post') {
            $.post(data.url, data.parameters, cuHandler.retrieveAjax, 'json');
        } else {
            $.get(data.url, data.parameters, cuHandler.retrieveAjax, 'json');
        }

    },

    isDisabled: function (e) {
        if ($(e).hasClass('disabled') || $(e).attr('disabled') != undefined) {
            return true;
        }
        return false;
    },

    // Retrieve information for AJAX-FORMS
    retrieveAjax: function (response, showAlert) {

        showAlert = showAlert == undefined ? true : showAlert;

        this.currentResponse = response;

        if (response.type == 'error') {

            if (response.modal_message != undefined) {
                bootbox.alert({
                    message: response.message
                });
            } else if (response.notify != undefined && response.message != undefined) {
                cuHandler.notify({
                    title: response.notify.title == undefined ? '' : response.notify.title,
                    type: response.notify.type == undefined ? 'alert-info' : response.notify.type,
                    icon: response.notify.icon == undefined ? 'svg-rmcommon-info-solid' : response.notify.icon,
                    text: response.message
                });
                response.notify = undefined;
            } else if (showAlert) {
                alert(response.message);
            }

        }

        if (response.token != undefined && response.token != '')
            $("#cu-token").val(response.token);

        cuHandler.closeLoader();

        cuHandler.checkAjaxAction(response);

        if (response.type == 'error')
            return false;

        return true;

    },

    /**
     * Check AJAX action reponsed.
     * @param data
     */
    checkAjaxAction: function (data) {

        /**
         * Ejecución de otras acciones
         */
        if (data.showMessage != undefined)
            alert(data.message);

        if (data.notify != undefined && data.message != undefined) {
            this.notify({
                title: data.notify.title == undefined ? null : data.notify.title,
                type: data.notify.type == undefined ? 'alert-info' : data.notify.type,
                icon: data.notify.icon == undefined ? 'svg-rmcommon-info-solid' : data.notify.icon,
                text: data.message
            })
        }

        // closeWindow: "#window-id"
        if (data.closeWindow != undefined)
            $(data.closeWindow).modal('hide');

        if (data.runHandler != undefined)
            eval(data.runHandler + "(data)");

        if (data.goto != undefined)
            window.location.href = data.goto;

        if (data.function != undefined)
            eval(data.function);

        if (data.openDialog != undefined) {

            cuHandler.modal.dialog({
                message: data.content,
                title: data.message,
                icon: data.icon != undefined ? data.icon : '',
                width: data.width != undefined ? data.width : '',
                owner: data.owner != undefined ? data.owner : '',
                id: data.windowId != undefined ? data.windowId : '',
                animate: false,
                closeButton: data.closeButton != undefined ? data.closeButton : true,
                color: data.color != undefined ? data.color : '',
                solid: data.solid != undefined ? true : false,
            });

        }

        if(data.dynamicTable != undefined){
            $(data.dynamicTable.table).dynamicTable(data.dynamicTable.action,{});
        }

        // Reload
        if (data.reload != undefined) {
            window.location.reload(true);
            return;
        }

    },

    showLoader: function () {

        $(".cu-window-loader").hide();
        $(".cu-window-blocker").hide();

        var html = '<div class="cu-window-blocker"></div>';
        html += '<div class="cu-window-loader">' +
            '<div class="loader-container text-center">' +
            '<button class="close" type="button">&times;</button>' +
            '<span>' +
            '<span class="cu-icon cu-spinner" style="animation: cu-spin 1s infinite steps(10)">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><path d="M5.233 2.056C5.23 1.063 6.033.253 7.027.25c.994-.003 1.803.8 1.806 1.794.004.994-.8 1.802-1.794 1.806-1 .003-1.81-.8-1.81-1.794zm3.824 1.568c-.003-.993.8-1.802 1.792-1.806.99-.003 1.8.8 1.8 1.793 0 1-.8 1.81-1.8 1.81-1 .01-1.8-.8-1.81-1.79zm2.495 3.81c-.002-.497.398-.9.896-.903.497 0 .902.4.904.9 0 .5-.402.9-.897.91-.498.01-.902-.39-.903-.89zm-1.57 3.824c-.003-.496.4-.9.895-.903.498 0 .903.4.904.897.01.498-.4.9-.89.904-.49.002-.9-.4-.9-.898zM6.17 12.853c-.002-.496.4-.9.897-.902.497 0 .9.4.903.9.002.5-.4.9-.897.91-.498.01-.902-.4-.903-.89zm-3.824-1.57c-.002-.495.4-.9.897-.902.496 0 .9.4.902.9.002.5-.4.9-.896.91-.5 0-.91-.4-.91-.9zM1.87 3.65c-.003-.746.6-1.352 1.345-1.354.746-.002 1.352.6 1.354 1.345 0 .75-.6 1.36-1.35 1.36-.75.01-1.35-.6-1.36-1.34zM.637 7.472c-.002-.56.45-1.014 1.01-1.016.56-.002 1.013.45 1.015 1.01 0 .56-.45 1.014-1.008 1.016S.64 8.03.638 7.472z"/></svg>' +
            '</span>' + ' ' + cuLanguage.inProgress + '</span>' +
            '</div></div>';

        $('body').append(html);

        $(".cu-window-blocker").fadeIn(0, function () {
            $(".cu-window-loader").fadeIn(1);
        });

    },

    closeLoader: function (handler) {
        $(".cu-window-loader").fadeOut(1, function () {
            $(".cu-window-blocker").fadeOut(0, function () {
                $(".cu-window-loader").remove();
                $(".cu-window-blocker").remove();
                if (handler != 'undefined')
                    handler;
            });
        });
    },

    getURI: function (module, controller, action, zone, params) {
        return this.getControllerURI(module, controller, action, zone, params);
    },

    getControllerURI: function (module, controller, action, zone, params) {

        var url = xoUrl;

        if (cu_modules[module] != undefined && controller != undefined && controller != '') {

            url += zone == 'backend' ? '/admin' : '';
            url += cu_modules[module] + '/' + controller + '/' + action + '/';

        } else {

            url += '/modules/' + module;

            url += zone == 'backend' ? '/admin' : '';
            if (controller == '' || controller == undefined)
                return url;

            url += '/index.php/' + controller + '/' + action + '/';

        }

        if (params == undefined)
            return url;

        var query = '';
        for (key in params) {
            query += (query == '' ? '?' : '&') + key + '=' + eval('params.' + key);
        }

        return url + query;

    },

    /**
     * Get the absolute or relative URL according to a given path
     * @param url
     * @param relative
     * @returns {string}
     */
    url: function (url, relative) {

        // Get the hostname
        var host = window.location.protocol + '//' + window.location.host;

        if (window.location.port != '') {
            host += ':' + window.location.port;
        }

        var baseUrl = xoUrl.replace(host, '');

        if (undefined == url) {
            return xoUrl;
        }

        if (arguments.length == 1 || true != relative) {
            return xoUrl + url;
        }

        return baseUrl + url;
    },

    /**
     * Crea una tabla de datos
     * @param ele
     */
    createDataTable: function (ele) {

        if (ele.hasClass("dataTable"))
            return;

        var exclude = $(ele).data("exclude");
        var cols = exclude != undefined ? exclude.toString().split(",") : '';

        $(ele).dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $(ele).data('source'),
            "bPaginate": true,
            //"aoColumnDefs": [exclude != undefined ? {"bSortable": false, "aTargets": cols} : '']
        });

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
    runAction: function (e) {

        var action = $(e).data("action");

        switch (action) {
            case 'load-remote-dialog':
            case 'load-module-dialog':
                cuHandler.loadRemoteDialog(e);
                break;
            case 'goto':
                var url = $(e).data("url");
                if (url == undefined)
                    url = $(e).attr("href");
                var retriever = $(e).data("retriever");

                if (retriever != undefined)
                    url = url + eval(retriever + '(e)');

                if (url != undefined && url != '')
                    window.location.href = url;
                break;
            case 'ajax':
                var url = $(e).attr('href') != '' ? $(e).attr('href') : $(e).data('url');
                cuHandler.requestAjax(e, {url: url});
            default:

                if ('' == action) {
                    return;
                }

                var parts = action.split('.');

                if (parts.length < 1) {
                    return;
                }

                if (parts.length == 1) {
                    if (undefined == window[parts[0]]) {
                        return;
                    }

                    window[parts[0]]();
                    return;
                }

                if (parts.length == 2) {
                    if (undefined == window[parts[0]][parts[1]]) {
                        return;
                    }

                    window[parts[0]][parts[1]]();
                }

                if (parts.length == 3) {
                    if (undefined == window[parts[0]][parts[1]][parts[2]]) {
                        return;
                    }

                    window[parts[0]][parts[1]][parts[2]]();
                }
                break;
        }

    },

    enableCommands: function (id_activator, type) {

        var commands = $("*[data-activator='" + id_activator + "']");

        var total = $("#" + id_activator + " :" + type + "[data-switch]:checked").length;

        $(commands).each(function (index) {

            var required = $(this).data("oncount") != undefined ? $(this).data("oncount") : ' >= 1';

            if (eval('total ' + required))
                $(this).enable();
            else
                $(this).disable();


        });

    },

    /*------------------------------------------------
     1.7 GET SVG ICON
     ------------------------------------------------*/
    getIcon: function (icon) {

        // Get an SVG icon from providers

        var isSVG = 'svg-' == icon.slice(0, 4);
        var isAbsolute = false;
        var isUrl = false;
        if (!isSVG) {
            isSVG = '.svg' == icon.slice(-4);

            if (isSVG) {
                isUrl = true;
            }
        }

        isAbsolute = null != icon.match(/^(http:\/\/|ftp:\/\/|https:\/\/\|\/\/)/i);

        if (isSVG && !isUrl) {
            var parts = icon.split("-");

            if (parts.length < 3) {
                return this.url("modules/rmcommon/icons/noicon.svg");
            }

            var fileName = icon.slice(5 + parts[1].length);

            var defaultFile = this.url("modules/rmcommon/icons/" + fileName + '.svg');

            if (undefined == iconsProviders) {
                return defaultFile;
            }

            if (iconsProviders.hasOwnProperty(parts[1])) {
                var file = iconsProviders[parts[1]] + '/' + fileName + '.svg';
                return file;
            }

            return defaultFile;
        }

        // SVG from URL
        if (isUrl && !isAbsolute) {
            return this.url(icon);
        }

        // Get an image
        var images = ['.jpg', '.gif', '.png', 'jpeg'];
        var ext = icon.slice(-4);

        if (images.indexOf(ext) || isAbsolute) {
            return icon;
        }

        // Get a icon from font
        return '<span class="' + icon + '"></span>';

    },

    /*------------------------------------------------
     1.8 LOAD ICON INSIDE CONTAINER
     ------------------------------------------------*/
    /**
     * Load an icon inside a container.
     * @param icon Icon path or name to use
     * @param container Container DOM element
     * @param replace Indicate if replace current xo-icon-svg or use existent
     * @returns {boolean}
     */
    loadIcon: function (icon, container, replace) {

        // We need two arguments
        if (arguments.length < 1) {
            return false;
        }

        replace = arguments.length == 2 ? true : (arguments.length == 1 ? false : replace);

        var file = this.getIcon(icon);

        var is_svg = file.slice(-4) == '.svg';
        var is_font = file.slice(0, 5) == '<span';

        if (replace) {
            var iconLoaded = container.find('.cu-icon');
        } else {
            var iconLoaded = $("<span />", {"class": 'cu-icon'});
        }

        if (undefined == iconLoaded || iconLoaded.length <= 0) {
            replace = false;
            iconLoaded = $("<span />", {"class": 'cu-icon'});
        }


        // Load a SVG icon
        if (is_svg) {
            iconLoaded.html('').load(file);
        } else if (is_font) {
            iconLoaded.html('').append(file);
        } else {
            /* If it is not a SVG icon then it's an image (?) */
            var img = $("<img>").attr("src", file);
            iconLoaded.html('').append(img);
        }

        if (!replace && arguments.length > 1) {
            $(container).append(iconLoaded);
        }

        return iconLoaded;

    },

    modal: bootbox,

    /**
     * This is a wrapper for PNotify plugin
     * See http://sciactive.com/pnotify/ for docs
     * @param options To be passed to PNotify plugin
     */
    notify: function (options) {

        //PNotify.prototype.options.styling = 'bootstrap3';
        return new PNotify(options);

    },


};

/**
 * Currenty format
 */
Number.prototype.formatMoney = function (c, d, t) {
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
jQuery.fn.enable = function () {
    this.each(function () {
        jQuery(this).removeAttr("disabled");
        jQuery(this).removeClass("disabled");
    });
}

jQuery.fn.disable = function () {
    this.each(function () {
        jQuery(this).prop("disabled", true);
        jQuery(this).addClass("disabled");
    });
}

$(document).ready(function () {

    var textarea_style = '';

    /**
     * Cargar diálogos de otros módulos
     */
    $('body').on('click', '*[data-action]', function (e) {
        if ($(this).is(":disabled") || $(this).attr("disabled") || $(this).hasClass('disabled')) {
            $(e).stopPropagation();
            return false;
        }
        cuHandler.runAction($(this));
        return false;

    });

    $("body").on('submit', 'form[data-type="ajax"]', function () {
        cuHandler.submitAjaxForm($(this));
        return false;
    });

    // Prevent submission of forms no-submit
    $("body").on('submit', 'form[data-type="no-submit"]', function () {
        return false;
    });

    $('body').on("click", ".cu-window-loader .close", function () {
        cuHandler.closeLoader();
    });

    $(".cu-data-table").each(function () {
        cuHandler.createDataTable($(this));
    });

    $("*[data-rel='tooltip']").tooltip();

    /**
     * Activar comandos
     */
    $("body").on('change', '.activator-container :checkbox[data-switch], .activator-container :radio[data-switch]', function () {

        event.stopPropagation();

        var id_container = $(this).parents(".activator-container").attr("id");

        cuHandler.enableCommands(id_container, $(this).attr("type"));

        if ($(this).attr("type") == 'radio') {
            $(this).parents(".activator-container").find(".tr-checked").removeClass('tr-checked');
        }

        if ($(this).is(":checked"))
            $(this).parents("tr").addClass('tr-checked');
        else
            $(this).parents("tr").removeClass('tr-checked');

    });

    /**
     * Select rows
     */
    $("body").on('click', '.activator-container > tbody > tr', function (e) {

        if (e.target.tagName != 'DIV' && e.target.tagName != 'TD') {
            return;
        }

        //var parent = $(this).parents("tr");
        var input = $(this).find("input[data-switch]");

        if ($(input).attr("type") == 'radio') {
            $(this).parents(".activator-container").find(".tr-checked").removeClass('tr-checked');
        }

        if (input.is(":checked"))
            input.removeAttr("checked");
        else
            input.prop("checked", 'checked');

        cuHandler.enableCommands($(this).parents(".activator-container").attr("id"), input.attr("type"));

        if ($(input).is(":checked"))
            $(this).addClass('tr-checked');
        else
            $(this).removeClass('tr-checked');

        event.stopPropagation();

    });

    /**
     * Select all checkbox
     */
    $("body").on("change", ":checkbox[data-checkbox]", function () {

        var checkbox_class = $(this).data("checkbox");

        if (checkbox_class == undefined)
            return;

        $(":checkbox[data-oncheck='" + checkbox_class + "']").prop('checked', $(this).prop('checked')).change();

        var $activator = $(this).parents('.activator-container');
        if ($activator.length > 0) {
            cuHandler.enableCommands($activator.attr("id"), 'checkbox');
        }

    });


    $("body").on("change", ':checkbox', function () {

        if (this.hasAttribute('data-checkbox'))
            return;

        if (!this.hasAttribute('data-oncheck'))
            return;

        var existing = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']");
        var checked = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']:checked");

        var activator = $(":checkbox[data-checkbox='" + $(this).data('oncheck') + "']");


        //if ( checked.length < existing.length )
        if (checked.length <= 0) {
            $(activator).removeAttr('checked');
        }
        //else if ( checked.length == existing.length )
        else if (checked.length > 0) {
            $(activator).prop('checked', 'checked');
        }

    });

    /**
     * Check if there are a "News" box in current page
     * and then, load the news for this module
     */
    if ($("*[data-news='load']").length == 1 || $("*[data-boxes='load']").length > 0) {

        var container = $("*[data-news='load']");
        if (container.length <= 0)
            container = $("*[data-boxes='load']");

        if (container.length <= 0)
            return false;

        var module = container.data('module');
        var target = $(container.data('target'));

        if (target != undefined)
            target.html('<div class="text-success"><span class="fa fa-spinner fa-pulse"></span> ' + cuLanguage.downloadNews + '</div>');

        var bcontainer = $("*[data-boxes='load']");
        var lang = $("html").attr('lang');
        lang = undefined == lang ? 'en' : lang;

        var params = {
            module: module,
            CU_TOKEN: $("#cu-token").val(),
            lang: lang
        };

        $.get(xoUrl + '/modules/rmcommon/ajax/module-info.php', params, function (response) {

            if (response.type == 'error') {
                target.html('<div class="text-danger"><span class="fa fa-exclamation-triangle"></span> ' + cuLanguage.downloadNewsError + '</div>')
                return;
            }

            /**
             * Get News
             */
            if (response.news != undefined && target != undefined) {

                news = $("<ul>").addClass("cu-ajax-news list-unstyled");
                for (i = 0; i < response.news.length; i++) {

                    var html = '<li>' +
                        '<small>' + response.news[i].date + '</small>' +
                        '<h5><a href="' + response.news[i].link + '" target="_blank">' + response.news[i].title + '</a></h5>';
                    /*if ( response.news[i].image )
                     html += '<img src="'+response.news[i].image+'" class="img-responsive">';*/

                    html += '<p class="help-block">' + response.news[i].content + '</p>' +
                        '</li>';
                    news.append(html);

                }
                target.html('').append(news);
                news.fadeIn('fast', function () {
                    $('html.dashboard [data-container="dashboard"]').trigger('containerUpdated');
                });

            }

            /**
             * Get boxes
             */
            if (response.boxes != undefined && bcontainer != undefined) {

                for (i = 0; i < response.boxes.length; i++) {

                    if (response.boxes[i].size == undefined || response.boxes[i].size <= 0) {
                        var size = 1;
                    } else {
                        size = response.boxes[i].size;
                    }

                    var box = $("<div data-dashboard=\"item\">").addClass('size-' + size).css("display", 'none');
                    box.append('<div class="cu-box ' + response.boxes[i].class + '"><div class="box-header">' +
                        '<span class="fa fa-caret-up box-handler"></span>' +
                        '<h3 class="box-title">' + response.boxes[i].title + '</h3></div>' +
                        '<div class="box-content">' + response.boxes[i].content + '</div></div>');
                    // Get the box position
                    if (response.boxes[i].container != undefined) {
                        var box_container = $(response.boxes[i].container);
                        if (box_container.length > 0) {
                            $(box_container).each(function () {
                                var newbox = box.clone();
                                if (response.boxes[i].position == 'top')
                                    $(this).prepend(newbox);
                                else
                                    $(this).append(newbox);
                                newbox.fadeIn('fast', function () {
                                    $('html.dashboard [data-container="dashboard"]').trigger('containerUpdated');
                                });

                            });
                        }
                    }

                }

            }


        }, 'json');

    }

    /**
     * Editor full screen
     */
    $("body").on('click', ".ed-container .full-screen", function () {
        $(this).parents(".ed-container").addClass('full-screen-edit');
        $(this).removeClass('full-screen').addClass('normal-screen');
        textarea_style = $(this).parents(".ed-container").find('.txtarea-container').attr("style");

        $(this).parents(".ed-container").find('.txtarea-container').attr("style", '');
        $("body").css("overflow", 'hidden');

    });
    $("body").on('click', ".ed-container .normal-screen", function () {
        $(this).parents(".ed-container").removeClass('full-screen-edit');
        $(this).addClass('full-screen').removeClass('normal-screen');
        $(this).parents(".ed-container").find('.txtarea-container').attr("style", textarea_style);
        textarea_style = '';
        $("body").css("overflow", 'visible');
    });

    /**
     * Set user notifications
     */
    $(".cu-notifications form .notification-item").change(function () {

        var event = $(this).data('event');
        var status = $(this).is(":checked") ? 1 : 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post(xoUrl + '/notifications.php', params, function (response) {

            if ('error' == response.type) {
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .animate({
                    backgroundColor: '#ffff99'
                }, 500, 'linear', function () {
                    $(this).animate({
                        backgroundColor: 'transparent'
                    }, 500, 'linear');
                });

        }, 'json');

    });

    $(" #cu-notifications .cancel-subscription").click(function () {

        var event = $(this).data('info');
        var status = 0;
        var item = $(this);

        var params = {
            event: event,
            status: status,
            page: 'cu-notification-subscribe'
        };

        $.post(xoUrl + '/notifications.php', params, function (response) {

            if ('error' == response.type) {
                alert(response.message);
                return;
            }

            $(item).parent().parent()
                .slideUp(250, function () {
                    $(this).remove();
                });

        }, 'json');

    });

});


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
//prepros-append 'rmtable.js';

var cuHandler = {

    /**
     * Propiedades
     */
    ismobile: /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()),

    currentResponse: false,

    checkEmail: function (email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    },

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
                    helpButton: response.helpButton != undefined ? response.helpButton : false,
                    helpUrl: response.helpUrl != undefined ? response.helpUrl : '#',
                    solid: response.solid != undefined ? true : false
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
                    text: undefined == response.notify.text ? response.message : response.notify.text
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
                text: undefined == data.notify.text ? data.message : data.notify.text
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
                helpButton: data.helpButton != undefined ? data.helpButton : false,
                helpUrl: data.helpUrl != undefined ? data.helpUrl : '#',
                color: data.color != undefined ? data.color : '',
                solid: data.solid != undefined ? true : false,
            });

        }

        if (data.dynamicTable != undefined) {
            $(data.dynamicTable.table).dynamicTable(data.dynamicTable.action, {});
        }

        // Reload
        if (data.reload != undefined) {
            window.location.reload(true);
            return;
        }

    },

    showLoader: function () {

        $(".cu-window-loader").remove();
        $(".cu-window-blocker").remove();

        var html = '<div class="cu-window-blocker"></div>';
        html += '<div class="cu-window-loader">' +
            '<div class="loader-container text-center">' +
            '<button class="close" type="button">&times;</button>' +
            '<span>' +
            '<span class="cu-icon cu-spinner" style="animation: cu-spin 1s infinite steps(10)">' +
            '<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
            '  viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">\n' +
            '    <path d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">\n' +
            '      <animateTransform \n' +
            '         attributeName="transform" \n' +
            '         attributeType="XML" \n' +
            '         type="rotate"\n' +
            '         dur="1s" \n' +
            '         from="0 50 50"\n' +
            '         to="360 50 50" \n' +
            '         repeatCount="indefinite" />\n' +
            '  </path>\n' +
            '</svg>' +
            '</span>' + ' ' + cuLanguage.inProgress + '</span>' +
            '</div></div>';

        $('body').append(html);
        $("div.cu-window-blocker").show();
        $("div.cu-window-loader").addClass('active');

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
            case 'register':
                this.registerForm({
                    item: $(e).data('item'),
                    type: $(e).data('type')
                });
                break;
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

        var total = $("#" + id_activator + " :" + type + ":checked").length;

        $(commands).each(function (index) {

            var required = $(this).data("oncount") != undefined ? $(this).data("oncount") : ' >= 1';

            if (eval('total ' + required))
                $(this).enable();
            else
                $(this).disable();


        });

    },

    registerForm: function (params) {

        if (undefined == params) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.dataInvalid
            });
            return false;
        }

        if (undefined == params.item) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.noItemRegister
            });
            return false;
        }

        if (undefined == params.type) {
            this.notify({
                type: 'alert-danger',
                icon: 'svg-rmcommon-error',
                text: cuLanguage.noTypeRegister
            });
            return false;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'form',
            dir: params.item,
            type: params.type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                return false;
            }

            if (undefined == response.form) {
                return false;
            }

            $("body")
                .addClass('registering')
                .append(response.form);

            $("#rmc-register-overlay").fadeIn(300, function () {
                $("#rmc-register-form").fadeIn(250);
            });

        }, 'json');

    },

    sendRegistration: function () {
        var $form = $("#rmc-register-form");
        var email = $form.find("#register-email").val();
        var api = $form.find("#register-api").val();
        var key = $form.find("#register-key").val();
        var type = $form.find("#register-type").val();
        var item = $form.find("#register-item").val();
        var error = false;

        if (undefined == email || '' == email || false == this.checkEmail(email)) {
            $form.find("#register-email").parent().addClass('error');
            error = true;
        }
        if (undefined == api || '' == api) {
            $form.find("#register-api").parent().addClass('error');
            error = true;
        }
        if (undefined == key || '' == key) {
            $form.find("#register-key").parent().addClass('error');
            error = true;
        }

        if (error) {
            cuHandler.notify({
                text: cuLanguage.thereAreErrors,
                icon: 'svg-rmcommon-error',
                type: 'alert-danger'
            });
            return;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'register',
            api: api,
            email: email,
            key: key,
            dir: item,
            type: type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                if (undefined != response.code && response.code == 'reactivate') {
                    cuHandler.showReactivation(response);
                }
                return false;
            }

            $("#rmc-register-form .form-group").fadeOut(250, function () {
                $(this).remove();
            });

            setTimeout(function () {
                $("#rmc-register-form").append(response.activation);
            }, 300);

        }, 'json');

    },

    showReactivation: function (response) {

        $("#rmc-register-form").fadeOut(250, function () {
            $(this).remove();
        });

        if(undefined == response.form || '' == response.form){
            cuHandler.notify({
                'text': cuLanguage.invalidResponse,
                'type': 'alert-warning',
                'icon': 'svg-rmcommon-alert'
            });
            return false;
        }

        $("body").append(response.form);
        $("#rmc-reactivate-form").fadeIn(300);

    },

    sendReactivation: function(){
        var $form = $("#rmc-reactivate-form");
        var email = $form.find("#reactivate-email").val();
        var api = $form.find("#reactivate-api").val();
        var key = $form.find("#reactivate-key").val();
        var type = $form.find("#reactivate-type").val();
        var item = $form.find("#reactivate-item").val();
        var license = $form.find("#reactivate-license").val();
        var error = false;

        if (undefined == key || '' == key) {
            $form.find("#reactivate-key").parent().addClass('error');
            error = true;
        }

        if (error) {
            cuHandler.notify({
                text: cuLanguage.thereAreErrors,
                icon: 'svg-rmcommon-error',
                type: 'alert-danger'
            });
            return;
        }

        var params = {
            CUTOKEN_REQUEST: $("#cu-token").val(),
            action: 'reactivate',
            api: api,
            email: email,
            key: key,
            license: license,
            dir: item,
            type: type
        };

        $.post(xoUrl + '/modules/rmcommon/ajax/register.php', params, function (response) {

            if (false == cuHandler.retrieveAjax(response)) {
                return false;
            }

            $("#rmc-reactivate-form .form-group, #rmc-reactivate-form .help-block").fadeOut(250, function () {
                $(this).remove();
            });

            setTimeout(function () {
                $("#rmc-reactivate-form").append(response.activation);
            }, 300);

        }, 'json');
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
        return new PNotify(options);
    },

    /**
     * Images manager launcher
     * <pre>
     * cuHandler.imagesManager({
     *     type: 'tiny|html|markdown|simple|external',
     *     target: 'container|function name',
     *     idContainer: 'container id',
     *     multiple: 'yes|no',
     *     title: 'title for modal'
     * }
     * </pre>
     */
    imagesManager: function (options) {

        var html = '<div id="blocker-' + options.idContainer + '" class="mgr_blocker"></div><div id="window-' + options.idContainer + '" class="imgmgr_container">';

        html += '<div class="window-title cu-titlebar"><button type="button" class="close">&times;</button>' + options.title + '</div>';
        html += '<iframe src="' + xoUrl + '/modules/rmcommon/include/tiny-images.php?type=' + options.type + '&amp;idcontainer=' + options.idContainer + '&amp;editor=' + options.idContainer + '&amp;target=' + options.target + '&amp;&amp;multi=' + options.multiple + '" name="image"></iframe>'
        html += '</div>';

        $("body").append(html);

        // window height


        $("#blocker-" + options.idContainer).fadeIn('fast', function () {
            $("body").css('overflow', 'hidden');
            $("#window-" + options.idContainer).fadeIn('fast', function () {

            });

        });

        $("#blocker-" + options.idContainer + ", #window-" + options.idContainer + " .window-title .close").click(function () {

            $("#window-" + options.idContainer).fadeOut('fast', function () {

                $("#blocker-" + options.idContainer).fadeOut('fast', function () {
                    $("body").css('overflow', 'auto');
                    $("#window-" + options.idContainer).remove();
                    $("#blocker-" + options.idContainer).remove();

                });

            })

        });

    },

    template: function (template, data) {

        var tpl = $.templates(template);
        return tpl.render(data);

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

    $("body").on('click', "#rmc-register-form > .close, #rmc-reactivate-form > .close", function () {
        $("#rmc-register-form, #rmc-reactivate-form").fadeOut(250, function () {
            $("#rmc-register-overlay").fadeOut(300, function () {
                $("#rmc-register-form, #rmc-reactivate-form").remove();
                $("#rmc-register-overlay").remove();
            });
        });
    });

    $("body").on('click', "#rmc-register-form button.btn", function () {
        cuHandler.sendRegistration();
    });

    $("body").on('click', "#rmc-reactivate-form button.btn", function () {
        cuHandler.sendReactivation();
    });

    $("body").on('keyup', "#rmc-register-form .form-group.error .form-control", function (e) {
        if (e.which != 13) {
            if ($(this).attr('name') == 'email') {
                if (cuHandler.checkEmail($(this).val())) {
                    $(this).parent().removeClass('error');
                }
                return;
            }
            if ('' != $(this).val()) {
                $(this).parent().removeClass('error');
                return;
            }
        }
    });

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

    $("body").on("change", ':checkbox[data-oncheck]', function () {

        /*if (this.hasAttribute('data-checkbox'))
            return;*/

        /*if (!this.hasAttribute('data-oncheck'))
            return;*/

        var total = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']").length;

        //var existing = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']");
        var checked = $(":checkbox[data-oncheck='" + $(this).data('oncheck') + "']:checked");

        var activator = $(":checkbox[data-checkbox='" + $(this).data('oncheck') + "']");

        //if ( checked.length < existing.length )
        if (checked.length < total && undefined == $(activator).attr('data-oncheck')) {
            $(activator).prop('checked', false);
        }  else if (checked.length >= total) {
            $(activator).prop('checked', true);
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


/**!
 * VControl for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      vcontrol
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

var warns = new Array();
var credentials = new Array();

(function ($) {

    this.UpdatesController = function () {

        var _self = this;

        this.init = function () {

            this.loadUpdates();

            /**
             * Shows FTP settings panel
             */
            $("#upds-ftp, #ftp-settings .btn-primary").click(function () {

                _self.ftpSettings();

            });

            /**
             * Cancel update on warning window
             */
            $("#upd-warning .cancel-warning").click(function () {
                _self.hideWarning();
            });

        };

        this.hideWarning = function(){

            $("#upd-warning").fadeOut('fast', function () {
                $("#upd-info-blocker").fadeOut('fast');
            });

        }

        /**
         * Shows the FTP settings panel
         */
        this.ftpSettings = function () {
            $("#ftp-settings").slideToggle('fast');
            if ($("#upds-ftp").hasClass("active"))
                $("#upds-ftp").removeClass('active');
            else
                $("#upds-ftp").addClass("active");
        };

        /**
         * Load current updates
         */
        this.loadUpdates = function () {

            $.get('updates.php', {action: 'ajax-updates'}, function (data) {

                if (undefined != data.token)
                    $("#cu-token").val(data.token);

                $("#rmc-updates").append(data);
                $(".rm-loading").fadeOut('fast');
                $("#rmc-updates > .upd-item").each(function () {
                    $(this).fadeIn('fast');
                });

            }, 'html');

        };

        /**
         * Starts the update process
         */

    };

})(jQuery);

var updates;

$(document).ready(function () {

    updates = new UpdatesController();
    updates.init();

    $("#upd-warning .continue-update").click(function () {

        $("#upd-warning .cancel-warning").click();

        var id = $(this).attr("data-id");
        if (id == undefined || id < 0) return;
        warns[id] = 1;
        installUpdate(id);

    });

    $("#upd-login .cancel-login, #upd-login .close").click(function () {

        $("#upd-login").fadeOut('fast', function () {
            $("#login-blocker").fadeOut('fast');
            $("#upd-login input").val('');
        });

    });

    $("#upd-login .ok-login").click(function () {

        if ($("#uname").val() == '') {
            $("#uname").addClass("error").focus();
            return;
        }

        if ($("#upass").val() == '') {
            $("#upass").addClass("error").focus();
            return;
        }

        $("#upd-login .cancel-login").click();
        var id = $(this).attr("data-id");
        if (id == undefined || id < 0) return;
        credentials[id] = $("#uname").val() + ':' + $("#upass").val();

        if ($("#upd-login").data('next') == 'download') {

            downloadUpdate(id);

        } else {

            installUpdate(id);

        }

    });

    $("body").on('click', '.button-details', function () {

        var id = $(this).data('id');

        $(this).cuSpinner({icon: 'svg-rmcommon-spinner-03'});

        loadUpdateDetails(id, $(this));

    });

});


function rmCheckUpdates() {

    $.get(xoUrl + '/modules/rmcommon/ajax/updates.php', {XOOPS_TOKEN_REQUEST: $("#cu-token").val()}, function (data) {

        if ('' != data.token)
            $("#cu-token").val(data.token);

        if (data.total <= 0) return false;

        rmCallNotifier(data.total);

    }, 'json');

}

function rmCallNotifier(total) {

    if (total <= 0) return;
    if (typeof updatesNotifier == 'function')
        updatesNotifier(total);

    if ($("#updater-info").length <= 0) return false;

    $("#updater-info").html($("#updater-info").html().replace("%s", total));
    $("#updater-info").fadeIn('fast');

}

function loadUpdateDetails(id, $element) {

    if (id == null || id == undefined) return false;

    var updates = eval($("#json-container").html());
    var update = updates[id].data;

    if (update.url == '') return false;
    if (update.url.match(/^http:\/\/|^https:\/\//) == null) return false;

    var url = update.url.replace(/\&amp;/, '&');

    $.get('updates.php', {action: 'update-details', url: url}, function (data) {

        $($element).cuSpinner();

        if (false == cuHandler.retrieveAjax(data)) {
            return false;
        }

    }, 'json');

}

function installUpdate(id) {

    if (id == null || id == undefined) return false;
    var updates = eval($("#json-container").html());
    var update = updates[id].data;
    if (update.url.match(/^http:\/\/|https:\/\//) == null) return false;

    var url = update.url.replace(/\&amp;/, '&');

    $("#upd-warning .continue-update").attr("data-id", id);

    if (update.warning != '' && warns[id] == undefined) {
        showWarning(update);
        return;
    }

    if (update.login == 1 && credentials[id] == undefined) {
        $("#upd-login .ok-login").attr("data-id", id);
        showLogin(update);
        return;
    }

    $("#upd-" + id + " .col-lg-4").hide();
    $("#upd-" + id + " .col-lg-8").removeClass('col-lg-8').addClass('col-lg-12');

    $("#upd-" + id).addClass('upd-item-process');

    $("#upd-" + id + " .upd-progress").slideDown('fast');

    updateStepOne(update, id);

}

/**
 * This function allows to download update package in order to install manually
 * @param id
 * @return {*}
 */
function downloadUpdate(id) {

    if (id == null || id == undefined) return false;

    var updates = eval($("#json-container").html());
    var update = updates[id].data;
    if (update.url.match(/^http:\/\//) == null) return false;

    var url = update.url.replace(/\&amp;/, '&');

    if (update.login == 1 && credentials[id] == undefined) {
        $("#upd-login .ok-login").attr("data-id", id);
        $("#upd-login").data("next", 'download');
        showLogin(update);
        return;
    }

    var params = {
        action: 'later',
        url: url,
        credentials: credentials[id] == undefined ? '' : credentials[id],
        type: update.type,
        dir: update.dir
    };

    $.post("updates.php", params, function (data) {

        if (data.error == 1) {
            alert(data.message);
            if ('' != data.token)
                $("#cu-token").val(data.token);
            return;
        }

        if ('' != data.token)
            $("#cu-token").val(data.token);

        $("#upd-" + id + " .button-later > i").removeClass("icon-spinner icon-spin").addClass("icon-time");

        window.location.href = "updates.php?action=getfile&file=" + data.data.file;

    }, 'json');

}

function installLater(id) {
    if (id == null || id == undefined) return false;

    $("#upd-" + id + " .button-later > i").removeClass("icon-time").addClass("icon-spinner icon-spin");

    var updates = eval($("#json-container").html());
    var update = updates[id].data;
    if (update.url.match(/^http:\/\//) == null) return false;

    var url = update.url.replace(/\&amp;/, '&') + '&action=download';

    if (update.login == 1 && credentials[id] == undefined) {
        $("#upd-login .ok-login").attr("data-id", id);
        $("#upd-login").data("next", 'download');
        showLogin(update);
        return;
    }

    downloadUpdate(id);
}

function showWarning(update) {

    $("#upd-info-blocker").fadeIn('fast');
    $("#upd-warning h4").html(update.title);
    $("#upd-warning p").html(update.warning);
    $("#upd-warning").fadeIn('fast');

}

function showLogin(update) {

    $("#login-blocker").fadeIn('fast', function () {
        var updates = eval($("#json-container").html());
        var id = $("#upd-login .ok-login").data("id");
        var update = updates[id].data;
        var a = document.createElement('a');
        a.href = update.url;
        $("#upd-login").fadeIn('fast', function () {
            $("#uname").focus();
        });
        $("#upd-login p").html($("#upd-login p").html().replace("%site%", '<a href="http://' + a.hostname + '" target="_blank">' + a.hostname + '</a>'));

    });

}

function updateStepOne(update, id) {

    var url = update.url.replace(/\&amp;/, '&');

    var params = {
        action: 'first-step',
        url: url,
        credentials: credentials[id] == undefined ? '' : credentials[id],
        type: update.type,
        dir: update.dir,
        ftp: $("#ftp-form").serialize(),
        'XOOPS_TOKEN_REQUEST': $("#cu-token").val()
    };

    incrementProgress('50%', id);

    $.post("updates.php", params, function (data) {

        if (data.error == 1) {
            $("#upd-" + id + " .upd-progress .status").html(data.message);
            $("#upd-" + id + " .progress-bar").addClass('progress-bar-danger');
            $("#upd-" + id + " .progress").removeClass("active");
            if ('' != data.token)
                $("#cu-token").val(data.token);
            return false;
        }

        if ('' != data.token)
            $("#cu-token").val(data.token);

        $("#upd-" + id + " .upd-progress .status").html(data.message);

        if (update.type == 'module' || update.type == 'plugin') {
            incrementProgress('80%', id);
            local_update(id);
        } else {

            incrementProgress('100%', id);
            $("#upd-" + id + " .progress-bar").addClass('progress-bar-success');
            $("#upd-" + id + " .progress").removeClass("active");
            $("#upd-" + id + " h4").addClass('update-done');

        }

        /*if(data.data.run!=undefined){
         incrementProgress('80%', id);
         runFiles(id, data.data.run);
         } else {
         incrementProgress('100%', id);
         $("#upd-"+id+" .progress-bar").addClass('progress-bar-success').removeClass("active");;
         $("#upd-"+id+" h4").addClass('update-done');
         }*/

    }, 'json');

}

function local_update(id) {

    var updates = eval($("#json-container").html());
    var update = updates[id].data;

    var params = {
        action: 'local-update',
        type: update.type,
        module: update.dir,
        XOOPS_TOKEN_REQUEST: $("#cu-token").val()
    };

    $.post('updates.php', params, function (response) {

        if (1 == response.error) {
            $("#upd-" + id + " .upd-progress .status").html(response.message);
            $("#upd-" + id + " .progress-bar").addClass('progress-bar-danger');
            $("#upd-" + id + " .progress").removeClass("active");
            if ('' != response.token)
                $("#cu-token").val(response.token);
            return false;
        }

        if ('' != response.token)
            $("#cu-token").val(response.token);

        cuHandler.modal.dialog({
            message: response.data.log,
            title: 'Module update log',
            width: 'large'
        });

        $("#upd-" + id + " .upd-progress .status").html(response.message);
        incrementProgress('100%', id);
        $("#upd-" + id + " .progress-bar").addClass('progress-bar-success');
        $("#upd-" + id + " .progress").removeClass("active");
        $("#upd-" + id + " h4").addClass('update-done');


    }, 'json');

}

function runFiles(id, run) {

    var files = eval(run);
    var total = files.length - 1;
    var start = 0;
    $("#files-blocker").fadeIn('fast', function () {

        $("#upd-run").fadeIn('fast', function () {

            $("#upd-run > iframe").attr("src", files[start]).load(function () {

                if (start < total) {
                    start++;
                    $(this).attr("src", files[start]);
                } else {
                    $("#upd-run").fadeOut('fast', function () {
                        $("#files-blocker").fadeOut('fast', function () {
                            $("#upd-" + id + ' .upd-progress .status').html(langUpdated);
                            incrementProgress('100%', id);
                            $("#upd-" + id + " .progress").addClass('progress-bar-success').removeClass("active");
                            ;
                            $("#upd-" + id + " h4").addClass('update-done');
                        });
                    });
                }

            })

        })

    })

}

function incrementProgress(p, id) {

    $("#upd-" + id + " .progress > .progress-bar").width(p);

}


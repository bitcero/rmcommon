function rmCheckUpdates(){$.get(xoUrl+"/modules/rmcommon/ajax/updates.php",{XOOPS_TOKEN_REQUEST:$("#cu-token").val()},function(t){if(""!=t.token&&$("#cu-token").val(t.token),t.total<=0)return!1;rmCallNotifier(t.total)},"json")}function rmCallNotifier(t){if(!(t<=0)){if("function"==typeof updatesNotifier&&updatesNotifier(t),$("#updater-info").length<=0)return!1;$("#updater-info").html($("#updater-info").html().replace("%s",t)),$("#updater-info").fadeIn("fast")}}function loadUpdateDetails(id,$element){if(null==id||void 0==id)return!1;var updates=eval($("#json-container").html()),update=updates[id].data;if(""==update.url)return!1;if(null==update.url.match(/^http:\/\/|^https:\/\//))return!1;var url=update.url.replace(/\&amp;/,"&");$.get("updates.php",{action:"update-details",url:url},function(t){if($($element).cuSpinner(),0==cuHandler.retrieveAjax(t))return!1},"json")}function installUpdate(id){if(null==id||void 0==id)return!1;var updates=eval($("#json-container").html()),update=updates[id].data;if(null==update.url.match(/^http:\/\/|https:\/\//))return!1;var url=update.url.replace(/\&amp;/,"&");if($("#upd-warning .continue-update").attr("data-id",id),""==update.warning||void 0!=warns[id]){if(1==update.login&&void 0==credentials[id])return $("#upd-login .ok-login").attr("data-id",id),void showLogin(update);$("#upd-"+id+" .col-lg-4").hide(),$("#upd-"+id+" .col-lg-8").removeClass("col-lg-8").addClass("col-lg-12"),$("#upd-"+id).addClass("upd-item-process"),$("#upd-"+id+" .upd-progress").slideDown("fast"),updateStepOne(update,id)}else showWarning(update)}function downloadUpdate(id){if(null==id||void 0==id)return!1;var updates=eval($("#json-container").html()),update=updates[id].data;if(null==update.url.match(/^http:\/\//))return!1;var url=update.url.replace(/\&amp;/,"&");if(1==update.login&&void 0==credentials[id])return $("#upd-login .ok-login").attr("data-id",id),$("#upd-login").data("next","download"),void showLogin(update);var params={action:"later",url:url,credentials:void 0==credentials[id]?"":credentials[id],type:update.type,dir:update.dir};$.post("updates.php",params,function(t){if(1==t.error)return alert(t.message),void(""!=t.token&&$("#cu-token").val(t.token));""!=t.token&&$("#cu-token").val(t.token),$("#upd-"+id+" .button-later > i").removeClass("icon-spinner icon-spin").addClass("icon-time"),window.location.href="updates.php?action=getfile&file="+t.data.file},"json")}function installLater(id){if(null==id||void 0==id)return!1;$("#upd-"+id+" .button-later > i").removeClass("icon-time").addClass("icon-spinner icon-spin");var updates=eval($("#json-container").html()),update=updates[id].data;if(null==update.url.match(/^http:\/\//))return!1;var url=update.url.replace(/\&amp;/,"&")+"&action=download";if(1==update.login&&void 0==credentials[id])return $("#upd-login .ok-login").attr("data-id",id),$("#upd-login").data("next","download"),void showLogin(update);downloadUpdate(id)}function showWarning(t){$("#upd-info-blocker").fadeIn("fast"),$("#upd-warning h4").html(t.title),$("#upd-warning p").html(t.warning),$("#upd-warning").fadeIn("fast")}function showLogin(update){$("#login-blocker").fadeIn("fast",function(){var updates=eval($("#json-container").html()),id=$("#upd-login .ok-login").data("id"),update=updates[id].data,a=document.createElement("a");a.href=update.url,$("#upd-login").fadeIn("fast",function(){$("#uname").focus()}),$("#upd-login p").html($("#upd-login p").html().replace("%site%",'<a href="http://'+a.hostname+'" target="_blank">'+a.hostname+"</a>"))})}function updateStepOne(t,a){var e={action:"first-step",url:t.url.replace(/\&amp;/,"&"),credentials:void 0==credentials[a]?"":credentials[a],type:t.type,dir:t.dir,ftp:$("#ftp-form").serialize(),XOOPS_TOKEN_REQUEST:$("#cu-token").val()};incrementProgress("50%",a),$.post("updates.php",e,function(e){if(1==e.error)return $("#upd-"+a+" .upd-progress .status").html(e.message),$("#upd-"+a+" .progress-bar").addClass("progress-bar-danger"),$("#upd-"+a+" .progress").removeClass("active"),""!=e.token&&$("#cu-token").val(e.token),!1;""!=e.token&&$("#cu-token").val(e.token),$("#upd-"+a+" .upd-progress .status").html(e.message),"module"==t.type||"plugin"==t.type?(incrementProgress("80%",a),local_update(a)):(incrementProgress("100%",a),$("#upd-"+a+" .progress-bar").addClass("progress-bar-success"),$("#upd-"+a+" .progress").removeClass("active"),$("#upd-"+a+" h4").addClass("update-done"))},"json")}function local_update(id){var updates=eval($("#json-container").html()),update=updates[id].data,params={action:"local-update",type:update.type,module:update.dir,XOOPS_TOKEN_REQUEST:$("#cu-token").val()};$.post("updates.php",params,function(t){if(1==t.error)return $("#upd-"+id+" .upd-progress .status").html(t.message),$("#upd-"+id+" .progress-bar").addClass("progress-bar-danger"),$("#upd-"+id+" .progress").removeClass("active"),""!=t.token&&$("#cu-token").val(t.token),!1;""!=t.token&&$("#cu-token").val(t.token),cuHandler.modal.dialog({message:t.data.log,title:"Module update log",width:"large"}),$("#upd-"+id+" .upd-progress .status").html(t.message),incrementProgress("100%",id),$("#upd-"+id+" .progress-bar").addClass("progress-bar-success"),$("#upd-"+id+" .progress").removeClass("active"),$("#upd-"+id+" h4").addClass("update-done")},"json")}function runFiles(id,run){var files=eval(run),total=files.length-1,start=0;$("#files-blocker").fadeIn("fast",function(){$("#upd-run").fadeIn("fast",function(){$("#upd-run > iframe").attr("src",files[start]).load(function(){start<total?(start++,$(this).attr("src",files[start])):$("#upd-run").fadeOut("fast",function(){$("#files-blocker").fadeOut("fast",function(){$("#upd-"+id+" .upd-progress .status").html(langUpdated),incrementProgress("100%",id),$("#upd-"+id+" .progress").addClass("progress-bar-success").removeClass("active"),$("#upd-"+id+" h4").addClass("update-done")})})})})})}function incrementProgress(t,a){$("#upd-"+a+" .progress > .progress-bar").width(t)}var warns=new Array,credentials=new Array;!function(t){this.UpdatesController=function(){var a=this;this.init=function(){this.loadUpdates(),t("#upds-ftp, #ftp-settings .btn-primary").click(function(){a.ftpSettings()}),t("#upd-warning .cancel-warning").click(function(){a.hideWarning()})},this.hideWarning=function(){t("#upd-warning").fadeOut("fast",function(){t("#upd-info-blocker").fadeOut("fast")})},this.ftpSettings=function(){t("#ftp-settings").slideToggle("fast"),t("#upds-ftp").hasClass("active")?t("#upds-ftp").removeClass("active"):t("#upds-ftp").addClass("active")},this.loadUpdates=function(){t.get("updates.php",{action:"ajax-updates"},function(a){void 0!=a.token&&t("#cu-token").val(a.token),t("#rmc-updates").append(a),t(".rm-loading").fadeOut("fast"),t("#rmc-updates > .upd-item").each(function(){t(this).fadeIn("fast")})},"html")}}}(jQuery);var updates;$(document).ready(function(){(updates=new UpdatesController).init(),$("#upd-warning .continue-update").click(function(){$("#upd-warning .cancel-warning").click();var t=$(this).attr("data-id");void 0==t||t<0||(warns[t]=1,installUpdate(t))}),$("#upd-login .cancel-login, #upd-login .close").click(function(){$("#upd-login").fadeOut("fast",function(){$("#login-blocker").fadeOut("fast"),$("#upd-login input").val("")})}),$("#upd-login .ok-login").click(function(){if(""!=$("#uname").val())if(""!=$("#upass").val()){$("#upd-login .cancel-login").click();var t=$(this).attr("data-id");void 0==t||t<0||(credentials[t]=$("#uname").val()+":"+$("#upass").val(),"download"==$("#upd-login").data("next")?downloadUpdate(t):installUpdate(t))}else $("#upass").addClass("error").focus();else $("#uname").addClass("error").focus()}),$("body").on("click",".button-details",function(){var t=$(this).data("id");$(this).cuSpinner({icon:"svg-rmcommon-spinner-03"}),loadUpdateDetails(t,$(this))})});
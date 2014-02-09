// $Id: dashboard.js 568 2010-12-24 06:08:58Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function get_mods_page(num){
    
    $("#loading-mods").show();
    $.post('index.php', {action: 'list', page: num}, function(data){
            
            $("#ajax-mods-list").html(data);
            $("#loading-mods").hide();
            
    },'html');   
    
}

$(document).ready(function(){

    var url=encodeURIComponent("http://www.xoopsmexico.net/modules/vcontrol/?id=1&limit=4");

    $.get('include/proxy.php', {url: url}, function(data){
        if(data.indexOf("<html")>0 && data.indexOf("</html>")>0) return;
        $("#rmc-recent-news").append(data);
        $("#loading-news").hide();
    }, 'html');

});
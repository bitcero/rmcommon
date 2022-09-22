/**
 * Modules helper for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   https://eduardocortes.mx
 * @link   https://rmcommon.bitcero.dev
 */

$(document).ready(function(){
    
    $(".mod_preinstall_container .th a").click(function(){
        var id = $(this).attr("id").replace("down-",'');
        $("#"+id+"-container").slideToggle(600);
    });
    
    $("#install-ok").click(function(){
		$("#install-form").submit();
    });
    
    $("a.uninstall_button").click(function(){

        var dir = $(this).data('dir');
        if (dir=='system') return;

        if (!confirm(message)) return;

        $("#mod-action").val("uninstall_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();

        return false;
    });
    
    $("a.update_button").click(function(){
        var dir = $(this).parent().parent().parent().attr('id').replace("module-",'');
        if (!confirm(message_upd)) return;
       
        $("#mod-action").val("update_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });
    
    $("a.disable_button").click(function(){
        var dir = $(this).parent().parent().parent().attr('id').replace("module-",'');
       
        if (!confirm(message_dis)) return;
       
        $("#mod-action").val("disable_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });
    
    $("a.enable_button").click(function(){
        var dir = $(this).parent().parent().parent().attr('id').replace("module-",'');
       
        $("#mod-action").val("enable_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });

    $("a.data_button").click(function(){
        var id = $(this).parents('tr').attr("id");
        var sdata = "#"+id+" .hidden_data";

        $("#info-module .header img").attr("src", $(sdata+" .image").html());
        $("#info-module .header h3").html($(sdata+" .oname").html());
        $("#info-module .header .desc").html($("#"+id+" .name .description").html());
        $("#info-module .version").html($(sdata+" .version").html());
        $("#info-module .dirname").html($(sdata+" .dirname").html());

        const author = $(sdata+" .author").html();

        if(author.length > 0){
            const author_data = JSON.parse(author);
            $("#info-module .author").html('');

            author_data.forEach(function(author){
                let html = '<div class="d-flex mb-2">';
                html += '<a href="'+author.url+'" target="_blank" class="me-2"><img src="' + author.img + '" title="' + author.aka + '"/></a>';
                html += '<div class="d-flex flex-column">';
                html += '<strong>'+author.name+'</strong>';
                html += '<small><a href="'+author.url+'" target="_blank" class="text-decoration-none text-dark">'+author.web+'</a></small>';
                html += '</div>';
                html += '</div>';
                $("#info-module .author").append(html);
            });
        }

        var web = '';
        if($(sdata+" .url").html()!=''){
            web = '<a href="'+$(sdata+" .url").html()+'" target="_blank">'+cuLanguage.visitWeb+'</a>';
            $("#info-module .web").html(web);
        }

        $("#info-module .license").html($(sdata+" .license").html());

        if($(sdata+" .help").html()!='')
            $("#info-module .help").html('<a href="'+$(sdata+" .help").html()+'" target="_blank" class="cu-help-button">'+$(sdata+" .oname").html()+'</a>');

        $("#info-module .social").html($(sdata+" .social").html());

        var $ele = $("#info-module");
        $ele.detach();

        $("body").append($ele);
        $ele.modal();

        return false;

    });

    $("#info-blocker, .info_close").click(function(){
        $("#info-module").fadeOut('fast', function(){
            $("#info-blocker").fadeOut('fast');
        });
    });
    
    $("a.rename").click(function(){
    	var el = $(this).parent().parent();
        var id = el.attr("id");
        $(this).fadeOut('fast');
        $("#"+el.attr("id")+" .the_name").fadeOut('fast', function(){
            var html = '<span class="renamer"><input type="text" name="newname" value="'+$("#"+id+" .hidden_data .name").html()+'" class="form-control newname" />';
            html += '<a href="#" class="cancelnewname" onclick="cancel_rename(\''+id+'\');"><span>Cancel</span></a>';
            html += '<a href="#" class="savenewname"><span>Save</span></a></span>';
            $("#"+el.attr("id")+" .the_name").html(html);
            $("#"+el.attr("id")+" .the_name").fadeIn('fast');
        });

        return false;

	});

    $(".the_name").on("click", ".cancelnewname", function(){
        var id = $(this).parent().parent().parent().parent().attr('id');
        var ele = $(this).parent();
        ele.fadeOut('fast', function(){
            var ele = $("#"+id+" td.name .the_name");
            ele.fadeOut('fast');

            if($("#"+id+" .hidden_data .adminlink").html()!='')
                ele.html('<a href="'+$("#"+id+" .hidden_data .adminlink").html()+'">'+$("#"+id+" .hidden_data .name").html()+'</a>');

            ele.fadeIn('fast', function(){
                $("#"+id+" .rename").fadeIn('fast');
            });
        });

    });

    $(".the_name").on("click", ".savenewname", function(){
        var dir = $(this).parent().parent().parent().parent().attr('id');

        var id = $("#"+dir+" .hidden_data .id").html();
        var name = $("#"+dir+" .renamer .newname").val();
        var ele = $(this).parent();

        if(name.replace(/^\s*|\s*$/g,"")==''){
            alert(message_wname);
            $("#"+dir+" .renamer .newname").focus();
            return false;
        }

        if(name==$("#"+dir+" .hidden_data .name").html()){
            alert(message_name);
            $("#"+dir+" .renamer .newname").focus();
            return false;
        }

        params = {
            name: name,
            id: id,
            action: 'savename',
            XOOPS_TOKEN_REQUEST: $('#XOOPS_TOKEN_REQUEST').val()
        };

        $.post('modules.php', params, function(data){

            if (data.error){
                alert(data.message);
                if (!data.token) window.location.reload();
            } else {

                ele.fadeOut('fast', function(){
                    var ele = $("#"+dir+" td.name .the_name");
                    ele.fadeOut('fast');

                    $("#"+dir+" .hidden_data .name").html(params.name);

                    if($("#"+dir+" .hidden_data .adminlink").html()!='' && $("#"+dir+" .hidden_data .active").html()=='1')
                        ele.html('<a href="'+$("#"+dir+" .hidden_data .adminlink").html()+'">'+$("#"+dir+" .hidden_data .name").html()+'</a>');
                    else
                        ele.html($("#"+dir+" .hidden_data .name").html());

                    ele.fadeIn('fast', function(){
                        $("#"+dir+" .rename").fadeIn('fast');
                        $("#"+dir+" td").effect('highlight', {}, 1000);
                    });
                });

            }

            if (data.token){
                $('#XOOPS_TOKEN_REQUEST').val(data.token);
            }



        }, "json");

    });
    
});

function show_module_info(id){
    $("body").append('<div id="mod-info-blocker"></div>');
    $("#mod-info-blocker").fadeIn('fast');
    $("body").append('<div id="mod-info-data"></div>');
    $("#mod-info-data").html($("#mod-"+id).html()).fadeIn('fast');
    $("body").css("overflow", 'hidden');
	//$("#mod-"+id).slideDown('fast');
}

function closeInfo( id ){
    $("#mod-info-data").fadeOut('fast', function(){
        $("#mod-info-blocker").fadeOut('fast', function(){
            $(this).remove();
            $("body").css("overflow", 'auto');
        });
        $(this).remove();
    });
}

function load_page(num){

    $("#img-load").show('slow');
    $("#mods-widget-container").slideUp('slow', function(){
        var params = {
            action: 'load_page',
            page: num,
            token: $("#token").val()
        }
        $.post('modules.php', params, function(data){
            $("#mods-widget-container").html(data);
            $("#img-load").hide('fast');
            $("#mods-widget-container").slideDown('slow');
        },'html');
    });
}
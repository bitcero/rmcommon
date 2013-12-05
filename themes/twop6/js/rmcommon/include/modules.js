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
        alert(dir);
        if (dir=='system') return;

        if (!confirm(message)) return;

        $("#mod-action").val("uninstall_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();

        return false;
    });
    
    $("a.update_button").click(function(){
        var dir = $(this).parent().parent().attr('id').replace("module-",'');
        if (!confirm(message_upd)) return;
       
        $("#mod-action").val("update_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });
    
    $("a.disable_button").click(function(){
        var dir = $(this).parent().parent().attr('id').replace("module-",'');
       
        if (!confirm(message_dis)) return;
       
        $("#mod-action").val("disable_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });
    
    $("a.enable_button").click(function(){
        var dir = $(this).parent().parent().attr('id').replace("module-",'');
       
        $("#mod-action").val("enable_module");
        $("#mod-dir").val(dir);
        $("#form-modules").submit();
    });

    $("a.data_button").click(function(){

        var id = $(this).parent().parent().attr("id");
        var sdata = "#"+id+" .hidden_data";

        $("#info-module .header img").attr("src", $(sdata+" .image").html());
        $("#info-module .header h3").html($(sdata+" .oname").html());
        $("#info-module .header .desc").html($("#"+id+" .name .descriptions").html());
        $("#info-module .thedata .version span").html($(sdata+" .version").html());
        $("#info-module .thedata .dirname span").html($(sdata+" .dirname").html());

        var author = '';
        if($(sdata+" .mail").html()!=''){
            author = '<a href="mailto:'+$(sdata+" .mail").html()+'">'+$(sdata+" .author").html()+'</a>';
        } else {
            author = $(sdata+" .author").html()
        }

        if(author!='')
            $("#info-module .thedata .author span").html(author);

        var web = '';
        if($(sdata+" .url").html()!=''){
            web = '<a href="'+$(sdata+" .url").html()+'" target="_blank">'+$(sdata+" .web").html()+'</a>';
            $("#info-module .thedata .web span").html(web);
        }

        $("#info-module .thedata .license span").html($(sdata+" .license").html());

        if($(sdata+" .help").html()!='')
            $("#info-module .thedata .help span").html('<a href="'+$(sdata+" .help").html()+'" target="_blank">'+$(sdata+" .oname").html()+'</a>');

        $("#info-blocker").fadeIn('fast', function(){
            $("#info-module").fadeIn('fast');
        });

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
            var html = '<span class="renamer"><input type="text" name="newname" value="'+$("#"+id+" .hidden_data .name").html()+'" class="newname" />';
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
	$("#mod-"+id).slideDown('fast');
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
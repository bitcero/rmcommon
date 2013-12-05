$(document).ready(function(){
	$("a.show").click(function(){
		
		var id = $(this).attr("id").replace("show-",'');
		
		position = $("#module-"+id).position();
		$("#data-display").hide();
		$("#data-display").css({'top':position.top+'px','left':position.left+'px'});
		
		// Fill data
		var data = "#data-display";
		var source = "#module-"+id;
		
		$(data+" .data_head .mod_image").html($(source+" .mod_image").html());
		$(data+" .data_head .name").html($(source+" .data_storage .realname").html());
		$(data+" .data_description").html($(source+" .data_storage .description").html());
		$(data+" table td.version").html($(source+" .data_storage .version").html());
		
		var author = '';

		if ($(source+" .data_storage .authormail").html()!='')
			author = '<a href="mailto:'+$(source+" .data_storage .authormail").html()+'">'+$(source+" .data_storage .author").html()+'</a>';
		else
			author = $(source+" .data_storage .author").html();
		
		var web = '';

		if ($(source+" .data_storage .authorurl").html()!='')
			web = '<a href="mailto:'+$(source+" .data_storage .authorurl").html()+'">'+$(source+" .data_storage .authorweb").html()+'</a>';
		else
			web = $(source+" .data_storage .authorweb").html();
		
		$(data+" table td.author").html(author);
		$(data+" table td.web").html(web);
		$(data+" table td.license").html($(source+" .data_storage .license").html());
		$(data+" table td.name").html($(source+" .data_storage .name").html());
        dir = $(source+" .data_storage .dirname").html();
        if (dir=='system'){
            $(data+" .data_buttons .uninstall_button").css("display",'none');
        } else {
            $(data+" .data_buttons .uninstall_button").css('display','');
        }
        
        $("#the-id").val($(source+" .data_storage .dirname").html());
        
        var active = $(source+" .data_storage .active").html()
        if (active==1){
            $(data+" .data_buttons .enable_button").hide();
            if (dir!='system')
                $(data+" .data_buttons .disable_button").show();
            else
                $(data+" .data_buttons .disable_button").hide();
        } else {
            $(data+" .data_buttons .enable_button").show();
            $(data+" .data_buttons .disable_button").hide();
        }
		
		$("#data-display").slideDown(200);
		
	});
    
    
    $(".mod_preinstall_container .th a").click(function(){
        var id = $(this).attr("id").replace("down-",'');
        $("#"+id+"-container").slideToggle(600);
    });
    
    $("#install-ok").click(function(){
        alert("Hola");
		$("#install-form").submit();
    });
    
    $("a.uninstall_button").click(function(){

       var dir = $(this).data('dir');
       if (dir=='system') return;
       
       if (!confirm(message)) return;
       
       $("#mod-action").val("uninstall_module");
       $("#mod-dir").val(dir);
       $("#form-modules").submit();
    });
    
    $("a.update_button").click(function(){
        var dir = $("#the-id").val();
       if (!confirm(message_upd)) return;
       
       $("#mod-action").val("update_module");
       $("#mod-dir").val(dir);
       $("#form-modules").submit();
    });
    
    $("a.disable_button").click(function(){
        var dir = $("#the-id").val();
       
       if (!confirm(message_dis)) return;
       
       $("#mod-action").val("disable_module");
       $("#mod-dir").val(dir);
       $("#form-modules").submit();
    });
    
    $("a.enable_button").click(function(){
        var dir = $("#the-id").val();
       
       $("#mod-action").val("enable_module");
       $("#mod-dir").val(dir);
       $("#form-modules").submit();
    });
    
    $("a.rename").click(function(){
    	var id = $(this).attr("id").replace("rename-",'');
    	$("#rename-blocker").show('fast', function(){
    		$("#rename span span").html($("#module-"+id+" .data_storage .name").html());
    		$("#rename-name").val($("#module-"+id+" .data_storage .name").html());
    		$("#id-module").val(id);
			$("#rename").show('slow', function(){
				$("#rename-name").focus();
			});
    	});
	});
	
	$("#rename-blocker").click(function(){
		$("#rename").hide('slow', function(){
			$("#rename-name").val('');
			$("#rename-blocker").hide('fast');
		});
	});
	
	$("#rename-save").click(function(){
		
		var id = $("#id-module").val();
		
		if ($("#rename-name").val() == $("#module-"+id+" .data_storage .name").html()){
			alert(message_name);
			$("#rename-name").focus();
			return;
		}
		
		params = {
			name: $("#rename-name").val(),
			id: $("#id-module").val(),
			action: 'savename',
			XOOPS_TOKEN_REQUEST: $('#XOOPS_TOKEN_REQUEST').val()
		};
		
		$.post('modules.php', params, function(data){
			
			if (data.error){
				alert(data.message);
				if (!data.token) window.location.reload();
				$("#rename-blocker").click();
			} else {
				
				$("#rename").hide('slow', function(){
					$("#rename-name").val('');
					$("#rename-blocker").hide('fast', function(){
						$("#module-"+data.id+" .mod_data .name a").html(params.name);
						$("#module-"+data.id+" .data_storage .name").html(params.name);
						$("#module-"+data.id).effect('highlight', {}, 1000);
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
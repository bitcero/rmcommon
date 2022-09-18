/**
 * Blocks controller for Common Utilities
 * @author Eduardo Cortés <i.bitcero@gmail.com>
 * @link   https://eduardocortes.mx
 * @link   https://rmcommon.bitcero.dev
 */

var blocksAjax = {

    loadForm: function(id,module){
        
        var params = {
            XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val(),
            module: module,
            block: id,
            action: 'settings'
        };

        // Show the screen blocker
        $("#settings-blocker").fadeIn('fast', function(){
            // Hides the scroll bar
            $('body').css("overflow", "hidden");
            // Shows ajax loader
            $("#settings-loading").fadeIn("fast", function(){
                // Send request

                $.post('ajax/blocks.php', params, function(data){

                    $("#settings-loading").fadeOut("fast", function(){

                        if(data.error){
                            blocksAjax.addMessage(data.message, 'alert-error');
                            blocksAjax.addToken(data.token);
                            return;
                        }

                        if(data.message!=undefined && data.message!='')
                            blocksAjax.addMessage(data.message, 'alert-info');

                        blocksAjax.addToken(data.token);

                        $("#settings-form-window")
                            .html(data.data.content)
                            .fadeIn("fast", function(){
                                $("#block-config-form").css({
                                    top: $("#settings-form-window .title").height() + $("#settings-form-window .settings-nav").height() + 20+'px',
                                    left: '20px',
                                    right: '20px',
                                    bottom: $("#settings-form-window .settings-form-controls").height() + 30+'px'
                                });
                            });

                        blocksAjax.eventChange();

                    });

                }, 'json');

            });
        });
        
        //blocksAjax.scrollId("tr-"+id);
        
    },

    scrollId: function(id){
        
        var pos = $("#"+id).position();
        
        $("html, body").animate({
            scrollTop: pos.top
        }, 2000);
        
    },
    
    eventChange: function(){

        //$.getScript("include/js/modules_field.js");
        $.ajax({
            url: "include/js/modules_field.js",
            dataType: 'script',
            cache: false
        });
    },
    
    close: function(){
        $("#settings-form-window").fadeOut("fast", function(){
            $("#settings-blocker").fadeOut('fast', function(){
                $("body").css("overflow", '');
            });
        });
    },
    
    sendConfig: function(){
        
        var vars = $("#frm-block-config").serialize();
        blocksAjax.close();
        
        $("#settings-form-window").fadeOut('fast', function(){
            $("#settings-loading").fadeIn("fast", function(){

                $.post("ajax/blocks.php", vars, function(data){
                    $("#settings-loading").fadeOut("fast", function(){
                        $("#settings-blocker").fadeOut('fast');
                    });

                    if(data.error){
                        blocksAjax.addMessage(data.message, 'alert-danger');
                        blocksAjax.addToken(data.token);
                        return;
                    }

                    if(data.message!=undefined && data.message!='')
                        blocksAjax.addMessage(data.message, 'alert-info');

                    blocksAjax.addToken(data.token);

                    var block = $("#block-"+data.data.id);
                    if(block.length<=0)
                        return;

                    if($(block).data("position")!=data.data.canvas){
                        blocksAjax.addWithOrder(data.data, block);
                    }

                    if(data.data.visible==1){
                        $("#block-"+data.data.id+" a.control-visible")
                            .data("action", 'hide')
                            .attr("title", cuLanguage.hideBlock)
                            .removeClass("text-success")
                            .addClass("text-warning")
                            .children("i")
                            .removeClass("fa-eye")
                            .addClass("fa-eye-slash");
                        $("#block-"+data.data.id).removeClass("invisible-block");
                    }else{
                        $("#block-"+data.data.id+" a.control-visible")
                            .data("action", 'show')
                            .attr("title", cuLanguage.showBlock)
                            .removeClass("text-warning")
                            .addClass("text-success")
                            .children("i")
                            .removeClass("fa-eye-slash")
                            .addClass("fa-eye");
                        $("#block-"+data.data.id).addClass("invisible-block");
                    }

                    blocksAjax.saveOrder($("#position-"+data.data.canvas));

                }, 'json');

            });
        });
        
    },

    addWithOrder: function(data, block){

        var blocks = $("#position-"+data.canvas+" > ol > li");

        $(block).data("position", data.canvas);

        if(blocks.length<=0)
            $("#position-"+data.canvas+" > ol").append(block);

        for(i=0;i<blocks.length;i++){

            if(i>data.weight){
                $(blocks[i-1]).before(block);
                return;
            }

        }

        $("#position-"+data.canvas+" > ol").append(block);

    },

    saveOrder: function(e){

        var list = e.length ? e : $(e.target);

        var params = {
            position: list.data("pos"),
            blocks: window.JSON.stringify(list.nestable('serialize')),
            action: 'save-orders',
            XOOPS_TOKEN_REQUEST: $("#token-positions").val()
        };

        $.post('ajax/blocks.php', params, function(data){

            if(blocksAjax.handleError(data))
                return;

            if(data.message != ''){
                // Position changes applied
                $("#position-"+data.data.position+" > ol > li").attr("data-position", data.data.position);
                blocksAjax.addMessage(data.message, 'alert-info');
                blocksAjax.showOkIcon("#position-"+data.data.position);

            }

        }, 'json');

    },

    addBlockToPosition: function(block){

        if(block.id == undefined || block.id<=0){
            blocksAjax.addMessage(cuLanguage.errorShowInPosition, 'alert-error');
            return false;
        }

        var html = '<li class="dd-item" data-id="'+block.id+'" id="block-'+block.id+'" data-position="'+block.position+'">';
        html += '<div class="row item-controls">';
        html += '<strong class="dd-handle">'+block.title+'</strong>';
        html += '<a href="#" class="pull-right text-error" data-block="'+block.id+'" data-action="delete" title="'+cuLanguage.deleteBlock+'"><i class="fa fa-minus-circle text-danger"></i></a>';

        if(block.visible)
            html += '<a href="#" class="pull-right text-warning" data-block="'+block.id+'" data-action="hide" title="'+cuLanguage.hideBlock+'"><i class="fa fa-eye-slash"></i></a>';
        else
            html += '<a href="#" class="pull-right text-success" data-block="'+block.id+'" data-action="show" title="'+cuLanguage.showBlock+'"><i class="fa fa-eye-slash"></i></a>';

        html += '<a href="#" class="pull-right" data-block="'+block.id+'" data-action="settings" title="'+cuLanguage.blockSettings+'"><i class="fa fa-wrench"></i></a>';
        html += '</div>';
        html += '</li>';

        if($("#position-"+block.canvas.id_position).length<=0)
            block.canvas.id_position = blocksAjax.getFirstPosition().data('id');

        if($("#position-"+block.canvas.id_position+" ol.dd-list").length<=0)
            $("#position-"+block.canvas.id_position).append('<ol class="dd-list"></ol>');

        $("#position-"+block.canvas.id_position+" ol.dd-list").append(html);

    },

    getFirstPosition: function(){

        var positions = $(".rmc-position-item");
        return $(positions[0]);

    },

    addMessage: function(message, type){

        var html = '<div class="row-fluid">';
        html += '<div class="alert '+type+'">';
        html += '<button type="button" class="close" data-dismiss="alert">×</button>';
        html += message+'</div></div>';

        $("#bk-messages").prepend(html);
        $("#blocks-console-control").effect("highlight",{}, 1000);

    },

    addToken: function(token, redirect){

        redirect = redirect==undefined ? true : false;

        if(redirect==true && (token==null || token==undefined || token==''))
            window.location.href = 'blocks.php';
        else
            $("#XOOPS_TOKEN_REQUEST").val(token);

    },

    changeVisibility: function(block, a){

        var params = {
            XOOPS_TOKEN_REQUEST: $("#token-positions").val(),
            action: $('#block-' + block).data("action") == 'show-block' ? $('#block-' + block).data("action") : 'hide-block',
            id: block
        };

        $.post('ajax/blocks.php', params, function(data){

            if(blocksAjax.handleError(data))
                return false;

            if(data.message!='')
                blocksAjax.addMessage(data.message, 'alert-info');

            blocksAjax.showOkIcon($('#block-' + block).data("position"));

            var visible = data.data.visible;

            $("#block-"+block).data("action", visible==1 ? 'hide-block' : 'show-block');
            $("#block-"+block+" .control-visible").attr("title", visible==1 ? cuLanguage.hideBlock : cuLanguage.showBlock)
                .removeClass(visible==1 ? 'text-success' : 'text-warning')
                .addClass(visible==1 ? 'text-warning' : 'text-success')
                .html('<i class="fa '+(visible==1 ? 'fa-eye-slash' : 'fa-eye')+'"></i>');

            if(visible)
                $('#block-' + block).removeClass("invisible-block");
            else
                $('#block-' + block).addClass("invisible-block");

        }, 'json');

    },

    showOkIcon: function(position){

        if(position==undefined || position=='')
            return false;

        $(position+' '+" h3 > .cu-icon").fadeIn('fast', function(){

            setTimeout(function(){
                $(position+" h3 > .cu-icon").fadeOut('fast');
            }, 1000);

        });

    },

    handleError: function(data, token){

        token = token==undefined ? true : token;

        if(data.error == 1){
            blocksAjax.addMessage(data.message + '<br>' + data.data.error, 'alert-error');
            if(token==true)
                blocksAjax.addToken(data.token);
            return true;
        }

        return false;

    },

    deleteBlock: function(block){

        if(!confirm(cuLanguage.deleteBlockMessage))
            return;

        var params = {
            id: block,
            XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val(),
            action: 'delete-block'
        };

        $.post('ajax/blocks.php', params, function(data){

            if(blocksAjax.handleError(data, true))
                return false;

            blocksAjax.addMessage(data.message, 'alert-warning');

            blocksAjax.addToken(data.token, true);

            var position = $("#block-" + block).data('position');
            $("#block-" + block).remove();
            blocksAjax.saveOrder($("#position-"+position));

            return true;

        }, 'json');

    }
    
}

$(document).ready(function(){
    
    $("#add-pos-menu").click(function(){
        $("#exspos").click();
    });

    jkmegamenu.definemenu("newban", "megamenu1", "click")
    jkmegamenu.definemenu("blocks-console-control", "blocks-console", "click")

    $("#newpos").click(function(){

        if($("#bks-and-pos").is(":visible")){

            $("#bks-and-pos").fadeOut('fast', function(){

                $("#blocks-list").fadeIn('fast');
                $("#newpos").html(cuLanguage.showPositions).parent().removeClass("active");

            });

        } else {

            $("#blocks-list").fadeOut('fast', function(){

                $("#bks-and-pos").fadeIn('fast');
                $("#newpos").html(cuLanguage.showBlocks).parent().addClass("active");

            });

        }

        return false;
    });
    
    $("#blocks-available li a").click(function(){

        var block = $(this).attr("id").replace("block-",'');
        
        var block = block.split("-");
        
        mod = block[0];
        id = block[1];
        
        var params = {
            XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val(),
            module: mod,
            block: id,
            action: 'insert'
        };
        
        $("#wait-buttons").fadeIn('slow');
        
        // Show blocks if not visible
        if(!$("#blocks-list").is(":visible")){
            $("#newpos").click();
        }
        
        $.post('ajax/blocks.php', params, function(data){

            if(data.error){
                blocksAjax.addMessage(data.message,  'alert-error');
                blocksAjax.addToken(data.token);
                return false;
            }
            
            if(data.message!=undefined && data.message!=''){
                blocksAjax.addMessage(data.message, 'alert-info');
            }

            blocksAjax.addToken(data.token);
            
            blocksAjax.addBlockToPosition(data.data.block);
            blocksAjax.loadForm(data.data.block.id);

            return false;
            
        }, 'json');

        return false;
        
    });
    

    
    $("a.edit_position").click(function(){
        
        var id = $(this).parent().parent().parent();
        var data = $("#"+$(id).attr("id")+" .pos_data");
        
        if($("#editor-row").length>0){
            $("#editor-row").hide();
            $("#editor-row").remove();
            $("tr").show();
        }
        
        var html = '<tr id="editor-row" style="display:none;" class="'+$(id).attr("class")+' editor" valign="top"><td>&nbsp;</td>';
        html += '<td><strong class="the_id">'+$(id).attr("id").replace("ptr-",'')+'</strong></td>';
        html += '<td><input type="text" name="name" id="ed-name" class="form-control" value="'+$(data).children('.name').html()+'"><br />';
        html += '<button type="submit" class="save_button btn btn-primary">'+cuLanguage.save+'</button>';
        html += '<button type="button" class="cancel_button btn btn-default">'+cuLanguage.cancel+'</button>';
        html += '</td>';
        html += '<td align="center"><input type="text" name="tag" id="ed-tag" class="form-control" value="'+$(data).children('.ptag').html()+'"></td>';
        html += '<td align="center"><input type="checkbox" name="active" id="ed-active" value="1" '+($(data).children('.active').html()=='1'?' checked="checked"' : '')+'" /></td>';
        html += '</tr>';
        $(id).after(html);
        $(id).hide();
        $("#editor-row").show();
        
        $("#editor-row .cancel_button").click(function(){
            
            $("#editor-row").hide();
            $("#editor-row").remove();
            $("tr").show();
            
        });
        
        $("#editor-row .save_button").click(function(){
            
            var params = {
                'action': 'savepos',
                'id': $("#editor-row .the_id").html(),
                'name': $("#editor-row #ed-name").val(),
                'tag': $("#editor-row #ed-tag").val(),
                'active': $("#editor-row #ed-active").is(":checked") ? '1' : 0,
                'XOOPS_TOKEN_REQUEST': $("#XOOPS_TOKEN_REQUEST").val()
            };
            
            $.post('ajax/blocks.php', params, function(data){

                if(blocksAjax.handleError(data.message, true))
                    return;

                blocksAjax.addMessage(data.message,'alert-info');

                blocksAjax.addToken(data.token, true);

                //$("#tr-"+id).after('<tr id="tr-block-form" class="even bk_trform" valign="top" style="display: none;"><td colspan="5">'+data.content+'</td></tr>');
                
                id = '#ptr-'+$("#editor-row .the_id").html();
                $(id+' .name').html($("#editor-row #ed-name").val());
                $(id+' .ptag').html($("#editor-row #ed-tag").val());
                if($("#editor-row #ed-active").is(":checked")){
                    $(id+" img.active").attr('src', 'images/done.png');
                    $(id+' .pos_data .active').html(1);
                } else {
                    $(id+" img.active").attr('src', 'images/closeb.png');
                    $(id+' .pos_data .active').html(0);
                }
                
                $("#editor-row").hide();
                $("#editor-row").remove();
                id = id.replace("#ptr-",'');
                $('#ptr-'+id).show();
                $('#ptr-'+id).effect('highlight',{}, 1000);
                
                return false;
            
            },'json');
            
            return false;
            
        });
        
    });

});

function control_action( action, block ){

    switch ( action ){
        case 'settings':
            blocksAjax.loadForm( block );
            break;
        case 'show':
            blocksAjax.changeVisibility( block, 'show-block' );
            break;
        case 'hide':
            blocksAjax.changeVisibility( block, 'hide-block' );
            break;
        case 'delete':
            blocksAjax.deleteBlock( block );
            break;
    }

    return false;

}

function before_submit(id){

	var types = $("tbody input");
	var go = false;

	for(i=0;i<types.length;i++){
		if ($(types[i]).is(":checked"))
			go = true;
	}

	if (!go){
		alert(cuLanguage.selectBefore);
		return false;
	}

	if ($("#bulk-topp").val()=='deletepos'){
		if (confirm(cuLanguage.confirmPositionDeletion)){
            $("#"+id).append('<input type="hidden" name="XOOPS_TOKEN_REQUEST" value="'+$("#XOOPS_TOKEN_REQUEST").val()+'">');
			$("#"+id).submit();
        }
	} else {
		$("#"+id).submit();
	}

    return false;

}

function select_option(id,action,form){
    
    if(form=='frm-positions')
        p = 'p';
    else
        p = '';

	if(action=='edit'){
		$("#bulk-topp").val('edit');
		$("#bulk-bottomp").val('edit');
		$("#"+form+" input[type=checkbox]").removeAttr("checked");
		$("#item-"+id).attr("checked","checked");
		$("#"+form).submit();
	}else if(action=='delete'){
		$("#bulk-topp").val('deletepos');
		$("#bulk-bottomp").val('deletepos');
		$("#"+form+" input[type=checkbox]").removeAttr("checked");
		$("#itemp-"+id).attr("checked","checked");
		before_submit(form);
	}

}
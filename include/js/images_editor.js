var total = 0;
var ids = new Array();
var url = '';
var current = 0;

$(document).ready(function(){
    $("div.container").hide();
    $("#upload-container").show();
    $("#img-toolbar a").click(function(){
        
        $("div.container").hide();
        $("#img-toolbar a").removeClass("select");
        $(this).addClass("select");
        
        id = $(this).attr("id").replace("a-",'');
        $("#"+id+"-container").show();
        
    });
    
});

function send_resize(id,params){
    $.get(url, {data: params, img: id, action: 'resize'}, function(data){
        
        if (data['error']){
            $("#resizer-bar span.message").html('<span>'+data['message']+'</span>');
            resize_image(params);
            return;
        }
        
        var img = '<img src="'+data['file']+'" alt="" title="'+data['title']+'" />';
        $("#gen-thumbnails").append(img);
        $("#resizer-bar span.message").html(data['message']+' - '+(current)+' of '+total);
        resize_image(params);
        
    }, "json");
    
}

function resize_image(params){
    
    if (ids.length<=0) return;    
    
    if(ids[current]==undefined){
        $("#bar-indicator").html('100%');
        $("#bar-indicator").animate({
            width: '100%'
        }, 100);
        current = 0;
        total = 0;
        ids = new Array();
        
        show_library();
        
        return;
    }
    
    percent = 1/total*100;
    
    send_resize(ids[current], params);
    $("#bar-indicator").animate({
        width: percent*(current)+'%'
    }, 100);
    //$("#bar-indicator").css('width', percent*(current+1)+'%');
    $("#bar-indicator").html(Math.round(percent*current+1)+'%');
    current++;
    
}

function show_upload(){
    
    $("#resizer-bar").hide('slow');
    $('.categories_selector').show('slow');
    $('#upload-errors').show('slow');
    $('#upload-controls').slideDown('slow');
    $("#bar-indicator").html('');
    $("#bar-indicator").css('width','0px');
    $("#gen-thumbnails").hide('slow', function(){
        $("#gen-thumbnails").html('');
    });
    
}

function show_library(pag){
    
    if ($("#ret-token").length>0) 
        $("#xoops-token").val($("#ret-token").val());
    
    if($("#category-field option").length==2){
		$("#category-field option").removeAttr("selected");
		var opt = $("#category-field option");
		$(opt[1]).attr("selected", 'selected');
    }

    var params = {
        category: $("#category-field").val(),
        action: 'load-images',
        XOOPS_TOKEN_REQUEST: $("#xoops-token").val(),
        url: window.parent.location.href,
        page: pag,
        type: $("#type").val(),
        name: $("#name").val(),
        target: $("#target").val(),
        idcontainer: $("#idcontainer").val(),
        custom: $("#parameters input").serialize()
    }
    
    $("#library-content").html('');
    $("#library-content").addClass('loading');
    
    $.post('tiny-images.php', params, function(data, status){
        $("#library-content").html(data);
        $("#library-content").removeClass('loading');
        
        $(".sizes input[type='radio']").click(function(){
            var iurl = $(this).val();
            var iid = $(this).attr('name').replace("size_",'');
            $("#data-"+iid+" .size_url").html('<span>'+iurl+'</span>');
        });
        
    }, 'html');
    
}

function show_image_data(id){
    
    $(".image_list").show();
    $(".image_data").hide();
    $("#list-"+id).hide();
    $("#data-"+id).show();
    
}

function hide_image_data(id){
    
    $(".image_list").show();
    $(".image_data").hide();
    
}

function insert_image(id,t,target,container){

    if(t=='xoops'){
        
        var rtn = '';
        var ext = $("#extension_"+id).val();
        
        if ($("#image-link-"+id).val()!=''){
            html = '[url='+$("#image-link-"+id).val()+']';
        }
        
        // Image
        html += '[img';
        var align = $("input[name='align_"+id+"']");
        for(i=0;i<align.length;i++){
            if(!$(align[i]).attr('checked')) continue;
            if(!$(align[i]).val()=='') continue;
            // File URL
            html += ' align='+$(align[i]).val();
        }
        html += ']';
        
        var sizes = $("input[name='size_"+id+"']");
        for(i=0;i<sizes.length;i++){
            if(!$(sizes[i]).attr('checked')) continue;
            
            // File URL
            html += $(sizes[i]).val();
        }
        
        html += '[/img]';
        if ($("#image-link-"+id).val()!=''){
            html += '[/url]';
        }
        
        exmPopup.insertText(html);
        exmPopup.closePopup();
        
        return;
    }
	
    if(t=='external'){
        
        var rtn = '';
        var ext = $("#extension_"+id).val();
        
        var sizesInput = $("input[name='size_"+id+"']");
        var sizes = new Array();
        var selected = '';
        
        $(sizesInput).each(function(i){
            sizes[i] = $(this).val();
            if($(this).is(":checked"))
                selected = $(this).val();
        });
        
        var data = {
            link: $("#image-link-"+id).val(),
            sizes: sizes,
            url: selected
        }
        
        eval("window.parent."+target+"(data, container)");
        return;
    }
	
	var html = '';
	var ext = $("#extension_"+id).val();
	
	// Link
	if ($("#image-link-"+id).val()!=''){
		html = '<a href="'+$("#image-link-"+id).val()+'" title="'+$("#image-name-"+id).val()+'">';
	}
	
	// Image
	html += '<img src="';
	var sizes = $("input[name='size_"+id+"']");
    var th = ''; var pw = 0;
    for(i=0;i<sizes.length;i++){

        if(pw==0){
            pw = $(sizes[i]).attr('rel');
            th = $(sizes[i]).val();
        } else {
            if ($(sizes[i]).attr('rel')<pw){
                pw = $(sizes[i]).attr('rel');
                th = $(sizes[i]).val();
            }
        }

        if(!$(sizes[i]).attr('checked')) continue;
        
        // File URL
        if(target=='container'){
            var sizeid = i;
            var imgcontainer = $(sizes[i]).val();
        }
        html += $(sizes[i]).val() + '"';
    }

    // Alignment
    var align = $("input[name='align_"+id+"']");
    for(i=0;i<align.length;i++){
        if(!$(align[i]).attr('checked')) continue;
        
        // File URL
        html += ' class="'+$(align[i]).val()+'"';
    }
    
    html += ' alt="'+$("#image-alt-"+id).val()+'" />';

    if ($("#image-link-"+id).val()!=''){
		html += '</a>';
	}

    if(target!=undefined && target=='container'){
         //window.parent.$("#"+container+" .thumbnail").hide();
        window.parent.$("#"+container+"-container .thumbnail").html('<a href="'+imgcontainer+'" target="_blank"><img src="'+th+'" /></a><input type="hidden" name="'+container+'" id="'+container+'" value="'+id+':'+sizeid+':'+encodeURIComponent($("#image-link-"+id).val())+':'+encodeURIComponent($("#image-name-"+id).val())+'" /><br /><a href="#" class="removeButton removeButton-'+container+'">Remove Image</a>');

        window.parent.$("#blocker-"+container).click();

        return;

    }

    ed = tinyMCEPopup.editor;
	ed.execCommand("mceInsertContent", true, html);
	tinyMCEPopup.close();
}

function insert_from_url(t){

    if($("#imgurl").val()==''){
        $("#imgurl").focus();
        return;
    }
    
    if(t=='xoops'){
        
        var rtn = '';
        if($("#url-link").val()!=''){
            rtn += "[url="+$("#url-link").val()+"]";
        }
        rtn += "[img";
        // Alignment
        var align = $("input[name='align_url']");
        for(i=0;i<align.length;i++){
            if(!$(align[i]).attr('checked')) continue;
            if($(align[i]).val()=='') continue;
            // File URL
            rtn += ' align='+$(align[i]).val();
        }
        
        rtn += ']'+$("#imgurl").val()+'[/img]';
        if($("#url-link").val()!=''){
            rtn += "[/url]";
        }
        
        exmPopup.insertText(rtn);
        exmPopup.closePopup();
        return;
    }
    
    ed = tinyMCEPopup.editor;
    
    var html = '';
    
    // Link
    if ($("#url-link").val()!=''){
        html = '<a href="'+$("#url-link").val()+'" title="'+$("#url-title").val()+'">';
    }
    
    // Image
    html += '<img src="'+$("#imgurl").val()+'" alt="'+$("#url-alt").val()+'"';
    
    // Alignment
    var align = $("input[name='align_url']");
    for(i=0;i<align.length;i++){
        if(!$(align[i]).attr('checked')) continue;
         if($(align[i]).val()=='') continue;
        // File URL
        html += ' class="'+$(align[i]).val()+'"';
    }
    
    html += ' />';
    
    if ($("#url-link").val()!=''){
        html += '</a>';
    }

    ed.execCommand("mceInsertContent", true, html);
    tinyMCEPopup.close();
    
}
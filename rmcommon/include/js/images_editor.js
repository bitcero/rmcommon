var total = 0;
var ids = new Array();
var url = '';
var current = 0;
var selected = new Array();

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

    $("body").on('click', '.thumbnail-item > .insert', function(){

        show_image_data( $(this).parent().data("id") );
        return false;

    })

    $("body").on('click', '.thumbnail-item > .add', function(){
        add_image_tray( $(this).parent().data("id") );
        return false;

    })

    /**
     * Enable border on thumbnail hover
     */

    $("body").on('mouseover', '.thumbnail-item', function() {

        $("#images-tray .img[data-id='"+$(this).data("id")+"']").addClass("mini-hover");

    });

    $("body").on('mouseout', '.thumbnail-item', function() {

        $("#images-tray .img[data-id='"+$(this).data("id")+"']").removeClass("mini-hover");

    });

    /**
     * Enable border on mini thumbnail hover
     */
    $("body").on('mouseover', '#images-tray .tray-added .img', function() {

        $(".thumbnail-item[data-id='"+$(this).data("id")+"']").addClass("thumbnail-hover");

    });

    $("body").on('mouseout', '#images-tray .tray-added .img', function() {

        $(".thumbnail-item[data-id='"+$(this).data("id")+"']").removeClass("thumbnail-hover");

    });

    /**
     * Remove selection
     */
    $("body").on('click', '.thumbnail-item.thumb-selected', function() {

        remove_from_tray( $(this).data("id") );

    });


    $("body").on( 'click', '#image-inserter > .content .img-links button', function(){

        $("#image-inserter > .content .img-link").val( $(this).data("link") );

    });

    $("body").on('click', "#image-inserter .btn-close", function(){
        $("#inserter-blocker").click();
    });

    $("body").on('click', "#images-tray .btn-clear", function(){
        $("#images-tray .tray-added .img").fadeOut( 250, function(){
            $(this).remove();
            $(".thumb-selected")
                .removeClass('thumb-selected')
                .find(".add")
                .removeClass("hidden")
                .fadeIn(250);
            selected = new Array();
            $("#images-tray").fadeOut(250, function(){
                $("body").animate({
                    paddingBottom: 10
                });
            });
        } );

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

    if ($("#ret-token").length>0)
        $("#xoops-token").val($("#ret-token").val());

    var params = {
        category: $("#category-field").val(),
        action: 'image-details',
        XOOPS_TOKEN_REQUEST: $("#xoops-token").val(),
        id: id,
        url: window.parent.location.href
    };

    $.post( 'tiny-images.php', params, function( data ){

        $("#inserter-blocker").fadeIn(250).click(function(){
            $("#image-inserter").fadeOut(250, function(){
                $("#inserter-blocker").fadeOut(250);
                $("body").css('overflow', 'auto');
            });
        });

        $("#image-inserter").fadeIn(250);
        $("body").css('overflow', 'hidden');

        $("#image-inserter > .content .image").css("background-image", "url('" + data.medium + "')");
        /* Author Info */
        $("#image-inserter .author-info .author-avatar").attr("src", data.author.avatar);
        $("#image-inserter .author-info a").attr("href", data.author.url);
        $("#image-inserter .author-info .media-heading a").html(data.author.uname);
        $("#image-inserter .author-info .info-date").html(data.date);

        /* IMage Info */
        $("#image-inserter .image-info .info-title").html(data.title);
        $("#image-inserter .image-info .info-description").html(data.description);
        $("#image-inserter .image-info .info-mime").html(data.mime);
        $("#image-inserter .image-info .info-original").html('<a href="'+data.original.url+'" target="_blank">' + data.original.file + '</a>');
        $("#image-inserter .image-info .info-size").html(data.original.size);
        $("#image-inserter .image-info .info-dimensions").html(data.original.width + ' x ' + data.original.height);

        $("#image-inserter > .content .img-title").val(data.title);
        $("#image-inserter > .content .img-description").val(data.description);
        $("#image-inserter > .content .img-link").val(data.original.url);

        $("#image-inserter > .content .img-links").html('');

        for ( var key in data.links ){

            $("#image-inserter > .content .img-links").append(
                '<button type="button" class="btn btn-default" data-link="'+ data.links[key].value + '">' + data.links[key].caption + '</button>'
            );

        }

        $("#image-inserter > .content .img-sizes").html('');
        for ( var key in data.sizes ){

            $("#image-inserter > .content .img-sizes").append(
                '<label><input type="radio" name="size" value="'+data.sizes[key].url+'"><span>' +
                    '<strong>'+data.sizes[key].name+'</strong><br>' +
                    '<small>('+data.sizes[key].width+' x '+data.sizes[key].height+')</small></span></label>'
            );

        }

        if ( data.token != undefined )
            $("#ret-token").val( data.token );

    } );

    return false;
    
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

        if(!$(sizes[i]).is(':checked')) continue;
        
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

/**
 * Add an image to images tray
 * @param integer id
 */
function add_image_tray( id ){

    if ( id == undefined || id <= 0 ){
        alert('No image to add');
        return false;
    }

    /**
     * Add image to tray
     */
    var src = $("#thumbnail-" + id);
    if ( src.length <= 0 )
        return false;

    $("#inserter-blocker").fadeIn(250);

    var img = $('<span class="img">');
    //img.css('background-image', src.css("background-image"));
    var bg_image = src.css("background-image");
    img.css('background-image', 'url(../images/wait.gif)');
    img.css('background-size', '16px 16px');
    img.attr("data-id", id);

    $("#images-tray > .tray-added > .images").append( img );
    img.fadeIn(350);
    src.find(".add").fadeOut(250).addClass('hidden');

    if ($("#ret-token").length>0)
        $("#xoops-token").val($("#ret-token").val());

    var params = {
        category: $("#category-field").val(),
        action: 'image-details',
        XOOPS_TOKEN_REQUEST: $("#xoops-token").val(),
        id: id,
        url: window.parent.location.href
    };

    /** Get data from server */
    $.post( 'tiny-images.php', params, function( data ){

        if ( data.error != undefined){

            alert( data.message != undefined ? data.message : 'An error ocurred' );
            src.find(".add").removeClass('hidden').fadeIn(250);
            remove_from_tray( id );
            return false;

        }

        selected[id] = data;

        if ( data.token != undefined )
            $("#ret-token").val( data.token );

        src.addClass('thumb-selected');
        img.css('background-size', '36px 36px');
        img.css( 'background-image', bg_image );

        $("#inserter-blocker").fadeOut(250);


    });

    $("#images-tray").fadeIn('fast', function(){

        $("body").animate(
            {paddingBottom: 100}
        );

    });

    return false;

}


/**
 * Remove an image from images tray
 * @param id
 */
function remove_from_tray( id ){

    if ( id == undefined || id <= 0 )
        return false;

    var tray = $("#images-tray .img[data-id='" + id + "']");
    if ( tray.length <= 0 )
        return false;

    $(tray).fadeOut( 250, function(){

        $(tray).remove();
        $("#thumbnail-" + id).removeClass( "thumb-selected" );
        $("#thumbnail-" + id + " .add").removeClass('hidden').fadeIn(250);
        delete selected[id];

        if ($("#images-tray .img").length <= 0)
            $("#images-tray").fadeOut(250, function(){
                $("body").animate({
                    paddingBottom: 10
                });
            });

    } );

}

/**
 * Get the image data and populate the array with information
 */
function load_image_data( id ){



}
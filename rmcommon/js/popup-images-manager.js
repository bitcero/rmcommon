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

    /**
     * Insert image button
     */
    $("body").on('click', "#image-inserter .btn-insert", function(){

        send_to_element( insert_image( read_inserter_data( $("#image-inserter .img-id").val() ) ) );
        window.parent.$("#blocker-" + $("#idcontainer").val() ).click();

    });

    /**
     * Insert multiple images
     */
    $("body").on('click', ".tray-commands .btn-insert", function(){

        insert_multiple_images();
        window.parent.$("#blocker-" + $("#idcontainer").val() ).click();

    });

    /**
     * Edit data image button
     */
    $("body").on('click', "#image-inserter .btn-edit", function(){

        set_image_data( 'no' );

    });


    /**
     * Edit an image in tray
     */
    $("body").on('click', ".tray-added .img", function(){

        edit_image_ontray( $(this).data("id") );

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
        editor: $("#editor").val(),
        name: $("#name").val(),
        target: $("#target").val(),
        multi: $("#multi").val(),
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

/**
 * Populates the fields of the image insert dialog.
 * @param id The identifier of the image
 * @param data The data as object
 */
function populate_inserter_data( id, data ){

    $("#image-inserter > .content .image").css("background-image", "url('" + data.medium + "')");
    $("#image-inserter > .content .img-id").val(id);
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
            '<label><input class="img-size" data-width="'+data.sizes[key].width+'" data-name="'+data.sizes[key].name+'" data-height="'+data.sizes[key].height+'" type="radio" name="size" value="'+data.sizes[key].url+'"><span>' +
                '<strong>'+data.sizes[key].name+'</strong><br>' +
                '<small>('+data.sizes[key].width+' x '+data.sizes[key].height+')</small></span></label>'
        );

    }

    $("#image-inserter > .content .img-sizes label:last-child input").prop( 'checked', 'checked' );

    if ( data.token != undefined )
        $("#ret-token").val( data.token );

}

/**
 * Shows the insert image dialog
 * @param action Specify if the dialog will insert the image or only edit information for an image in tray.
 */
function show_image_inserter( action ){

    action = action == undefined || action == 'insert' ? 'insert' : 'edit';

    $("#inserter-blocker").fadeIn(250).click(function(){
        $("#image-inserter").fadeOut(250, function(){
            $("#inserter-blocker").fadeOut(250);
            $("body").css('overflow', 'auto');
        });
    });

    $("#image-inserter").fadeIn(250);
    $("body").css('overflow', 'hidden');

    if ( action == 'edit' ){
        $("#image-inserter .btn-insert").hide();
        $("#image-inserter .btn-edit").show();
        $("#image-inserter .btn-edit-next").show();
    } else {
        $("#image-inserter .btn-edit").hide();
        $("#image-inserter .btn-edit-next").hide();
        $("#image-inserter .btn-insert").show();
    }

}

function hide_image_inserter(){

    $("#image-inserter").fadeOut(250, function(){
        $("#inserter-blocker").fadeOut(250);
        $("body").css('overflow', 'auto');
    });

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

        if ( data.error != undefined){

            alert( data.message != undefined ? data.message : 'An error ocurred' );
            src.find(".add").removeClass('hidden').fadeIn(250);
            remove_from_tray( id );
            return false;

        }

        populate_inserter_data( id, data );

        show_image_inserter( 'insert' );

    } );

    return false;
    
}

function hide_image_data(id){
    
    $(".image_list").show();
    $(".image_data").hide();
    
}

/**
 * Insert or sends an image to the specified target
 * @param id Image identirifer
 * @param t Type of target: xoops (XOOPS Editor), external (External target) or any other (tiny, html, etc.)
 * @param target Target function where data will be send
 * @param container Local container where selected image will be inserted
 */
function insert_image( data, multiple ){

    if ( data == undefined )
        return false;

    multiple = multiple == undefined || multiple == 'no' ? false : true;

    var id = data.id;
    var type = $("#type").val();
    var target = $("#target").val();
    var container = $("#idcontainer").val();

    if(type == 'exmcode'){
        
        var rtn = '';
        var ext = $("#extension_"+id).val();
        
        if (data.link != ''){
            html = '[url='+data.link+']';
        }
        
        // Image
        html += '[img';
        html += data.align != '' ? ' align=' + data.align : '';
        html += ']';
        
        var sizes = data.size;
        html += sizes;
        
        html += '[/img]';
        if (data.link){
            html += '[/url]';
        }

        return html;
        
    }
	
    if(type == 'external'){

        var insert_data = {
            link: data.link,
            url: data.size,
            title: data.title,
            alt: data.alt,
            description: data.description,
            align: data.align,
            thumbnail: data.thumbnail,
            id: id
        }

        return insert_data;

    }
	
	var html = '';
	
	// Link
	if (data.link!=''){
		html = '<a href="'+data.link+'" title="'+data.title+'">';
	}
	
	// Image
	html += '<img src="' + data.size + '"';

    // Alignment
    html += data.align != '' ? ' class="'+data.align+'"' : '';

    html += data.alt != '' ? ' alt="'+data.alt+'"' : '';

    html += '>';

    if (data.link != ''){
		html += '</a>';
	}

    if(target!=undefined && target=='container'){
         //window.parent.$("#"+container+" .thumbnail").hide();
        var accept = window.parent.$("#"+container+"-container").data("accept");

        if ( multiple )
            //window.parent.$("#"+container+"-container .thumbnail").append(
            return '<div style="margin-bottom: 10px; text-align: center;">' +
                    '<a href="'+data.size+'" target="_blank">' +
                    '<img src="'+(accept == 'thumbnail' ? data.thumbnail : data.size)+'">' +
                    '</a>' +
                    '<input type="hidden" name="'+container+'[]" id="'+container+'" value="'+id+':'+data.name+':'+encodeURIComponent(data.link)+':'+encodeURIComponent(data.title)+'">' +
                    '<a href="#" class="btn btn-warning btn-xs" onclick="$(this).parent().remove(); return false;">Remove Image</a></div>';
        else
            //window.parent.$("#"+container+"-container .thumbnail").html(
            return '<a href="'+data.size+'" target="_blank"><img src="'+(accept == 'thumbnail' ? data.thumbnail : data.size)+'" /></a><input type="hidden" name="'+container+'" id="'+container+'" value="'+id+':'+data.name+':'+encodeURIComponent(data.link)+':'+encodeURIComponent(data.title)+'" /><br /><a href="#" class="removeButton removeButton-'+container+'">Remove Image</a>';

    }

    return html;

}

function send_to_element( data ){

    var id = data.id;
    var type = $("#type").val();
    var target = $("#target").val();
    var container = $("#idcontainer").val();

    if ( type == 'exmcode' ){

        exmPopup.insertText(data);
        exmPopup.closePopup();

    } else if ( type == 'html'){

        window.parent.edInsertContent( container, data );

    } else if ( type == 'external' ){

        eval("window.parent."+target+"(data, container)");

    } else if ( type == 'simple' ){

        insert_at_cursor( window.parent.$("#" + container), data );

    } else if ( target!=undefined && target=='container' ){

        if ( $("#multi").val() == 'yes' )
            window.parent.$("#"+container+"-container .thumbnail").append( data );
        else
            window.parent.$("#"+container+"-container .thumbnail").html( data );

    } else {

        if ( tinyMCEPopup.editor != null ){
            ed = tinyMCEPopup.editor;
            ed.execCommand("mceInsertContent", true, data);
            tinyMCEPopup.close();
        } else {
            ed = tinyMCE.get( $("#idcontainer").val() );
        }


    }

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

        populate_inserter_data( id , data );
        show_image_inserter( 'edit' );


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

function edit_image_ontray( id ){

    if ( selected[id] == undefined )
        return;

    var data = selected[id];
    populate_inserter_data( id, data );
    show_image_inserter( 'edit' );

}

/**
 * Set the image data when multi files will be inserted
 * @param next Specify if the inserter dialog must show the next image when save data
 */
function set_image_data( next ){

    next = next == undefined || next == 'no' ? false : true;
    var id = $("#image-inserter .img-id").val();

    var inserter = {
        id: id,
        type: $("#type").val(),
        func: $("#target").val(),
        container: $("#idcontainer").val()
    };

    var data = read_inserter_data( inserter.id );

    // Set new data
    selected[id].title = data.title;
    selected[id].alt = data.alt;
    selected[id].description = data.description;
    selected[id].link = data.link;
    selected[id].align = data.align;
    selected[id].size = data.size;
    selected[id].thumbnail = data.thumbnail;

    if ( next ){

    } else{

        hide_image_inserter();

    }


}

/**
 * Reads the current insert imaga dialog fields values
 * @param id
 * @returns {*}
 */
function read_inserter_data( id ){

    if ( !$("#image-inserter").is(":visible") )
        return false;

    if ( $("#image-inserter .img-id").val() != id )
        return false;

    var data = {
        id: $("#image-inserter .img-id").val(),
        title: $("#image-inserter .img-title").val(),
        alt: $("#image-inserter .img-alt").val(),
        description: $("#image-inserter .img-description").val(),
        link: $("#image-inserter .img-link").val(),
        align: $("#image-inserter .img-align:checked").val(),
        size: $("#image-inserter .img-size:checked").val(),
        name: $("#image-inserter .img-size:checked").data('name')
    };

    var width = 0;
    $("#image-inserter .img-size").each(function( i ){

        if ( i == 0 || $(this).data('width') < width )
            data.thumbnail = $(this).val();

    });

    return data;

}

function insert_multiple_images(){

    var data = '';
    var dobj = [];
    var temp;

    for ( var key in selected ){

        if ( selected[key] == undefined )
            continue;

        temp = insert_image( selected[key], 'yes' );
        if ( temp !== null && typeof temp === 'object' )
            dobj[dobj.length] = temp;
        else
            data += temp;

    }

    send_to_element( dobj.length>0?dobj:data );

}

/**
 * Added for simple editor support
 * @param myField
 * @param myValue
 */
function insert_at_cursor(element, text) {
    //IE support
    if (document.selection) {
        element.focus();
        sel = document.selection.createRange();
        sel.text = text;
    }

    //MOZILLA/NETSCAPE support
    else if ($(element)[0].selectionStart || $(element)[0].selectionStart == '0') {
        var startPos = $(element)[0].selectionStart;
        var endPos = $(element)[0].selectionEnd;
        $(element).val( $(element).val().substring(0, startPos)
            + text
            + $(element).val().substring(endPos, $(element).val().length));
    } else {
        $(element).val(text);
    }
}
/*!
 Advanced Form Fields for Common Utilities
 @author: Eduardo Cortés (AKA bitcero)
 @url: http://www.redmexico.com.mx
 @license: GPL 2
*/

// @prepros-prepend 'icon-picker.js'
// @prepros-append 'fields/icons-selector.js'
// @prepros-append 'fields/repeater-field.js'
// @prepros-append 'fields/countries-field.js'
// @prepros-append 'fields/select2.js'
// @prepros-append 'fields/users-field.js'

var total = rmwebfonts.items!=undefined ? rmwebfonts.items.length : 0;
var sliderCounter = [];

(function($){

    $(document).ready(function(){

        $(".adv-icons-picker").advIconPicker({icons:'glyphicons'});

        /**
         * WebFonts Control
         * Next events are related to WebFonts control
         */
        $("body").on('change', '.rm_webfonts_container select', function(){

            var id = $(this).attr("id").replace("selector-",'');

            if($(this).val()==''){
                $("#"+id).val('');
                $("#webfont-"+id+" .control").slideUp(250);
                $("#webfont-previewer-"+id).attr("href", '');
                $("#webfont-"+id+" .btn-show").data("status", 'hidden').prop("disabled", 'disabled').find(".fa").removeClass("fa-caret-up").addClass("fa-caret-down");
                return;
            }

            $("#webfont-"+id+" .btn-show").removeAttr("disabled");

            webfonts_load_data(id);

            if ( $("#webfont-"+id+" .btn-show").data('status') == 'hidden' )
                $("#webfont-"+id+" .btn-show").click();

            // Populate related field if exists
            $('#selector-' + id + '-name').val($(this).val().replace(/\+/g,' '));
            $('input[data-rel="conf_' + $(this).attr("name").replace("selector-", '') + '"]').val("'" + $(this).val().replace(/\+/g,' ') + "'");

        });

        $('body').on('click', ".rm_webfonts_container .btn-show", function(){

            var id = $( this).data( "id" );

            var sel = 'selector-' + id.replace("webfont-",'');

            if ( $("#" + sel).val() == '')
                return false;

            $("#" + id + " .control").slideToggle('250');

            if ( $(this).data( "status" ) == 'hidden' ){
                $(this).find(".fa").removeClass("fa-caret-down").addClass("fa-caret-up");
                $(this).data("status", 'visible');
            } else {
                $(this).find(".fa").removeClass("fa-caret-up").addClass("fa-caret-down");
                $(this).data("status", 'hidden');
            }

        } );

        $('body').on('change', '.rm_webfonts_container input:checkbox', function(){

            var id = $(this).parents('.rm_webfonts_container').attr("id").replace("webfont-",'');
            var base = 'https://fonts.googleapis.com/css?family='+$("#webfont-"+id+ " select").val().replace(/\s/g,"+");
            var styles = '';
            var tc = 0; // Total checked
            var regular = false;
            var tocheck = '';

            // Select variants
            $("#webfont-"+id+" .font-variants input").each(function(i){

                val = $(this).val();
                regular = val=='regular' ? true : regular;
                tocheck = i==0 || val=='300' ? val : tocheck;

                if($(this).is(":checked")){
                    tc++;
                    variant = $(this).val()=='regular' ? '400' : ($(this).val()=='italic' ? '400italic' : $(this).val());
                    styles += styles=='' ? ':'+variant : ','+variant;

                }

            });

            if(tc==0){

                tocheck = regular ? 'regular' : tocheck;
                $("#webfont-"+id+" .font-variants input[value='"+tocheck+"']").attr("checked",'checked');
                styles = regular ? '' : ':'+tocheck;

            }

            styles = styles==':400' ? '' : styles;

            // Characters
            var latin = false;
            var subsets = '';
            tc = 0;
            $("#webfont-"+id+" .font-subsets input").each(function(i){
                var val = $(this).val();
                latin = val=='latin' ? true : latin;
                tocheck = i==0 ? val : tocheck;

                if($(this).is(":checked")){
                    tc++;
                    subsets += subsets=='' ? val : ','+val;

                }

            });

            if(tc==0){

                tocheck = latin ? 'latin' : tocheck;
                $("#webfont-"+id+" .font-subsets input[value='"+tocheck+"']").attr("checked",'checked');
                subsets = regular ? '' : tocheck;

            }

            subsets = subsets=='latin' ? '' : subsets;

            $("#webfont-"+id+ " .font-value > div").html(base + styles + (subsets!='' ? '&amp;subset='+subsets : ''));
            $("#"+id).val( $("#webfont-"+id+ " .font-value > div").html());

        });

        // Select default data
        $(".rm_webfonts_container > input[type='hidden']").each(function(){

            var id = $(this).attr("id");

            if($(this).val()!=''){

                var defvals = $(this).val().replace('https://fonts.googleapis.com/css?family=','').split("&");
                // Variants
                var lvariants = defvals[0].split(":");

                if(lvariants[0]!='') {
                    $("#selector-" + id).val(lvariants[0]);
                    $("#selector-" + id + '-name').val(lvariants[0].replace(/\+/g,' '));
                }

                webfonts_load_data(id);

                variants = lvariants[1]!=undefined ? lvariants[1].split(",") : new Array();

                $("#webfont-"+id+" .font-variants input").each(function(){

                    val = $(this).val()=='regular' ? '400' : ($(this).val()=='italic' ? '400italic' : $(this).val());

                    if(jQuery.inArray(val, variants)>-1)
                        $(this).attr("checked",'checked');
                    else
                        $(this).removeAttr("checked");

                });

                if(defvals[1]!=undefined){

                    var subsets = defvals[1].replace("subset=",'').split(',');

                    $("#webfont-"+id+" .font-subsets input").each(function(){
                        if(jQuery.inArray($(this).val(), subsets)>-1)
                            $(this).attr("checked",'checked');
                        else
                            $(this).removeAttr("checked");

                    });

                }

                $(".rm_webfonts_container > .font-variants input[checked='checked']").change();

            }

        });

        /**
         * Image URL
         * Next events are related to image url control
         */
        $("body").on('click', '.adv_img_launcher', function(){

            var id = $(this).attr("data-id");

            var html = '<div id="blocker-'+id+'" class="advmgr_blocker"></div><div id="window-'+id+'" class="advmgr_container">';
            html += '<div class="window-title cu-titlebar"><span></span>'+imgmgr_title+'</div>';
            html += '<iframe src="'+mgrURL+'?target=advInsertUrl&amp;idcontainer='+id+'&amp;type=external&amp;multi=no" name="image"></iframe></div>'
            html += '</div>';
            $("body").append(html);

            // window height


            $("#blocker-"+id).fadeIn('fast', function(){
                $("body").css('overflow','hidden');
                $("#window-"+id).fadeIn('fast', function(){

                });

            });

            $("#blocker-"+id+", #window-"+id+" .window-title span").click(function(){

                $("#window-"+id).fadeOut('fast', function(){

                    $("#blocker-"+id).fadeOut('fast', function(){
                        $("body").css('overflow','auto');
                        $("#window-"+id).remove();
                        $("#blocker-"+id).remove();

                    });

                })

            });

        });

        /**
         * Update thumbnail when input changed
         */
        $("body").on('blur', ".adv_imgurl input", function(){

            imgUrlInsert($(this));

        });

        /**
         * SLIDER FIELD
         */
        $(".adv-slider-container").each(function(i){

            var id = $(this).attr("id");
            sliderCounter[i] = $("#"+id+" .adv-one-slider").length;

        });

        $("body").on('click', '.adv-slider-container .edit', function(){

            //id = $(this).attr("data-id") + ' > .adv-one-slider';
            button = $(this);

            $(button).parent().parent().slideUp('fast', function(){
                $(this).parent().removeClass("slider-collapse").addClass('slider-expanded');
                $(this).parent().children(".the-controls").slideDown('fast', function(){
                    $(this).parent().children(".the-buttons").slideDown('fast');
                })

            });

        });

        $("body").on('click', '.adv-slider-container .delete', function(){

            $(this).parent().parent().parent().remove();
            $(id).remove();
        });

        $("body").on('click', '.adv-slider-container .close', function(){

            id = $(this).attr("data-id") + ' > .adv-one-slider';
            button = $(this);

            $(this).parent().slideUp('fast', function(){
                $(button).parent().parent().children(".the-controls").slideUp('fast', function(){
                    $(this).parent().removeClass("slider-expanded").addClass('slider-collapse');
                    $(this).parent().children(".the-options").slideDown('fast');
                })

            });

        });

        $("body").on('keyup', '.adv-slider-container .the-title', function(){
            $(this).parent().parent().children(".the-options").children(".title").html($(this).val());
        });

        $("body").on('click', '.adv-slider-container > .button-add > button', function(){

            id = $(this).parent().parent().attr("id");
            index = getSlideCount(id);
            data = $("#"+id+" > .data-fields").html();
            eval("data = "+data);

            html = '<div class="adv-one-slider" data-id="'+index+'">';
            html += '<div class="the-buttons">';
            html += '<button type="button" class="close button buttonPurple" data-id="imgurl-'+id+'-'+index+'">'+advFormLang.close+'</button>';
            html += '</div>';
            html += '<div class="the-options input-group">';
            html += '<span class="title form-control"></span>';
            html += '<span class="input-group-btn"><button class="edit btn btn-default" type="button" data-id="'+index+'"><span class="fa fa-edit"></span> '+advFormLang.edit+'</button>';
            html += '<button class="delete btn btn-warning" type="button" data-id="'+index+'"><span class="fa fa-times"></span> '+advFormLang.delete+'</button></span>';
            html += '</div>';
            html += '<div class="the-controls">';

            fields = data.fields;
            name = data.name;

            var ic = 0;
            for(var fid in fields){
                html += '<div class="form-group"><label for="'+id+'-'+fid+'-'+index+'">'+fields[fid].caption+'</label>';
                switch(fields[fid].type){
                    case 'textarea':
                        html += '<textarea name="'+name+'['+index+']['+fid+']" rows="5" class="form-control"></textarea>';
                        break;

                    case 'imageurl':
                        html += '<div class="adv_imgurl" id="iurl-container-'+id+'-'+index+'"><div class="input-group">';
                        html += '<input type="text" name="'+name+'['+index+']['+fid+']" id="imgurl-'+id+'-'+index+'" value="" size="10" class="form-control">';
                        html += '<span class="adv_img_launcher input-group-btn" data-id="imgurl-'+id+'-'+index+'" data-title="'+advFormLang.insertImage+'">' +
                            '<button type="button" class="btn btn-default">...</button></span>';
                        html += '</div>';
                        html += '<div class="img-preview"><img id="preview-imgurl-'+id+'-'+index+'" src="" /></div></div>';
                        break;
                    case 'textbox':
                    default:
                        html += '<input type="text" name="'+name+'['+index+']['+fid+']"'+(ic==0?' class="the-title form-control"':'')+' />';
                        break;
                }
                html += '</div><input type="hidden" name="'+name+'['+index+'][adv-field-types]['+fid+']" value="'+fields[fid].type+'">';
                html += '<input type="hidden" name="'+name+'['+index+'][adv-field-captions]['+fid+']" value="'+fields[fid].caption+'">';
            }

            html += '</div>';
            html += '</div>';

            $("#"+id+" > .data-fields").before(html);

        });

        $(".adv-color-chooser span.chooser").each(function(i){
            setColorPicker($(this));
        });

        $("body").on('keyup', '.adv-color-chooser input', function(){

            sharp = $(".adv-color-chooser input").attr("data")!=undefined;

            hex = $(this).val().replace(/#/g,'');
            id = "adv-color-"+$(this).attr("id");
            $("#"+id+" .color_launcher").css('background-color', "#"+hex);

        })

    });
}(jQuery));

function setColorPicker(ele){

    var container = $(ele).parent().attr("id");

    $(ele).ColorPicker({
        color: $("#"+container+" input").val(),
        onShow: function(c){
            $(c).fadeIn('fast');
            return false;
        },
        onHide: function(c){
            $(c).fadeOut('fast');
            return false;
        },
        onChange: function(hsb, hex, rgb){
            $(ele).parents('.adv-color-chooser').find('.previewer').css('background-color', "#"+hex);
            sharp = $("#"+container+" input").attr("data");
            sharp = sharp==undefined ? '' : sharp;
            $("#"+container+" input").val(sharp+hex);
        },
        onSubmit: function(hsb, hex, rgb, el) {
            $(ele).parents('.adv-color-chooser').find('.previewer').css('background-color', '#'+hex);
            sharp = $("#"+container+" input").attr("data");
            sharp = sharp==undefined ? '' : sharp;
            $("#"+container+" input").val(sharp+hex);
            $(el).ColorPickerHide();
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor( $("#"+container+" input").val());
        }
    });

}

function getSlideCount(id){

    var rid = 0;
    $(".adv-slider-container").each(function(i){

        if($(this).attr("id")!=id) return;

        if(sliderCounter[i]==undefined)
            sliderCounter[i] = 0;
        else
            sliderCounter[i]++;

        rid = sliderCounter[i];

    });

    return rid;

}

function imgUrlInsert(ele){

    id = $(ele).attr("id");

    if($("#preview-"+id).attr("src")==$(ele).val()) return;

    if($(ele).val()==''){
        $("#preview-"+id).fadeOut('fast', function(){
            $("#preview-"+id).attr('src','');
        });
        return;
    }

    var url = $(ele).val();

    $(ele).addClass("input-loading");

    $("#preview-"+id).fadeOut('fast', function(){

        $("#preview-"+id).attr('src', '');
        $("#preview-"+id).bind('load', function(){
            $(this).fadeIn('fast');
            $("#"+id).removeClass("input-loading");
        });
        $("#preview-"+id).attr('src',url);
    });

}

function webfonts_load_data(id){

    var select = $("#selector-"+id);
    var parent = $("#webfont-"+id);

    /* $(parent).find(".font-variants").fadeOut('fast');
     $(parent).find(".font-preview").fadeOut('fast');

     $(parent).find(".font-subsets").fadeOut('fast', function(){*/

    for(i=0;i<total;i++){

        if(rmwebfonts.items[i].family.replace(/\s/g,'+') == $(select).val()){

            var tv = rmwebfonts.items[i].variants.length;
            html = '';
            regular = false;
            first = 'regular';
            for(v=0;v<tv;v++){

                regular = rmwebfonts.items[i].variants[v]=='regular' || rmwebfonts.items[i].variants[v]=='italic' ? true : regular;
                first = v==0 || rmwebfonts.items[i].variants[v]=='300' ? rmwebfonts.items[i].variants[v] : first;

                value = rmwebfonts.items[i].variants[v]=='regular' ? 'Normal 400' : '';
                value = rmwebfonts.items[i].variants[v]=='italic' ? 'Normal 400 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='100' ? 'Ultra-Light 100' : value;
                value = rmwebfonts.items[i].variants[v]=='100italic' ? 'Ultra-Light 100 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='200' ? 'Light 200' : value;
                value = rmwebfonts.items[i].variants[v]=='200italic' ? 'Light 200 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='300' ? 'Book 300' : value;
                value = rmwebfonts.items[i].variants[v]=='300italic' ? 'Book 300 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='500' ? 'Medium 500' : value;
                value = rmwebfonts.items[i].variants[v]=='500italic' ? 'Medium 500 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='600' ? 'Semi-Bold 600' : value;
                value = rmwebfonts.items[i].variants[v]=='600italic' ? 'Semi-Bold 600 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='700' ? 'Bold 700' : value;
                value = rmwebfonts.items[i].variants[v]=='700italic' ? 'Bold 700 Italic' : value;
                value = rmwebfonts.items[i].variants[v]=='800' ? 'Extra-Bold 800' : value;
                value = rmwebfonts.items[i].variants[v]=='800italic' ? 'Extra-Bold 800 Italic' : value;

                style = 'font-family:'+"'"+$(select).val().replace(/\+/g,' ')+"';";
                style += rmwebfonts.items[i].variants[v].indexOf("italic")!=-1 ? 'font-style: italic;' : '';
                style += rmwebfonts.items[i].variants[v]!='regular' ? 'font-weight: '+rmwebfonts.items[i].variants[v].replace("italic", '')+';' : '';

                html += '<label style="'+style+'"><input type="checkbox" name="variant[]" value="'+rmwebfonts.items[i].variants[v]+'" /> '+value+'</label>';
            }

            $(parent).find(".font-variants").children('div').html(html);

            first = regular==true ? 'regular' : first;

            var ts = rmwebfonts.items[i].subsets.length;
            html = '';
            for(s=0;s<ts;s++){
                html += '<label><input type="checkbox" name="subset[]" value="'+rmwebfonts.items[i].subsets[s]+'" /> '+rmwebfonts.items[i].subsets[s]+'</label>';
            }

            $(parent).find(".font-subsets").children('div').html(html);
            //$(parent).children(".font-preview").children('div').html($(select).val());

            $("#webfont-"+id+" .font-value > div").html('https://fonts.googleapis.com/css?family='+$(select).val().replace(/\s/g,"+")+(!regular ? ':'+first : ''));
            $("#"+id).val($("#webfont-"+id+" .font-value > div").html());
            $("#webfont-"+id+" .font-use > div").html("font-family: '"+$(select).val().replace(/\+/g,' ')+"';");

            /*$(parent).children(".font-variants").fadeIn('fast');
             $(parent).children(".font-subsets").fadeIn('fast');
             $(parent).children(".font-preview").fadeIn('fast');
             $(parent).children(".font-value").fadeIn('fast');
             $(parent).children(".font-use").fadeIn('fast');*/

            $("#webfont-"+id+" .font-variants label input[value='"+first+"']").attr("checked", 'checked');
            $("#webfont-"+id+" .font-subsets label input[value='latin']").attr("checked", 'checked');
            $("#webfont-previewer-"+id).attr("href", $("#webfont-"+id+" .font-value > div").html());
            $(parent).children(".font-preview").children("div").css('font-family', "'"+$(select).val().replace(/\+/g,' ')+"'");

            break;
        }
    }

    //});

}

/**
* Insert image url in RMFormImageUrl control
*/
function advInsertUrl(data, id){

    $("#blocker-"+id).click();
    $("#"+id).val(data.url);
    imgUrlInsert($("#"+id));

}
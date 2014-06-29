$(document).ready(function(){

    $("body").on("click", ".image_manager_launcher", function(){
        var id = $(this).parent().attr("id").replace("-container",'');
        var html = '<div id="blocker-'+id+'" class="mgr_blocker"></div><div id="window-'+id+'" class="imgmgr_container">';

        var imgmgr_title = imgmgr_title == undefined ? 'Seleccionar imagen' : '';
        var multiple = $("#"+id+'-container').data('multiple');
        multiple = multiple == undefined || multiple == 'no' ? 'no' : 'yes';

        html += '<div class="window-title cu-titlebar"><button type="button" class="close">&times;</button>'+imgmgr_title+'</div>';
        html += '<iframe src="'+mgrURL+'?target=container&amp;idcontainer='+id+'&amp;multi='+multiple+'" name="image"></iframe>'
        html += '</div>';
        $("body").append(html);

        // window height


        $("#blocker-"+id).fadeIn('fast', function(){
            $("body").css('overflow','hidden');
            $("#window-"+id).fadeIn('fast', function(){

            });

        });

        $("#blocker-"+id+", #window-"+id+" .window-title .close").click(function(){

            $("#window-"+id).fadeOut('fast', function(){

                $("#blocker-"+id).fadeOut('fast', function(){
                    $("body").css('overflow','auto');
                    $("#window-"+id).remove();
                    $("#blocker-"+id).remove();

                });

            })

        });

    });

    $(document).on('click', ".removeButton", function(){

        var id = $(this).parent().parent().attr("id").replace("-container",'');
        $(this).parent().html('<input type="hidden" name="'+id+'" id="'+id+'" value="" />');
        return false;

    });

});
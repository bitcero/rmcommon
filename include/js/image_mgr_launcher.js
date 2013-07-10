$(document).ready(function(){

    $(".image_manager_launcher").click(function(){
        var id = $(this).parent().attr("id").replace("-container",'');
        var html = '<div id="blocker-'+id+'" class="mgr_blocker"></div><div id="window-'+id+'" class="imgmgr_container">';
        html += '<div class="th"><span></span>'+imgmgr_title+'</div>';
        html += '<div class="imgmgr_content"><iframe src="'+mgrURL+'?target=container&amp;idcontainer='+id+'" name="image"></iframe></div>'
        html += '</div>';
        $("body").append(html);

        // window height


        $("#blocker-"+id).fadeIn('fast', function(){
            $("body").css('overflow','hidden');
            var h = $(window).height();
            var nh = h-$("#window-"+id+" .th").height()-30;
            $("#window-"+id+" iframe").css('height', nh+'px');
            $("#window-"+id).fadeIn('fast', function(){

            });

        });

        $("#blocker-"+id+", #window-"+id+" .th span").click(function(){

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
/**
* $Id$
*/
$(document).ready(function(){
    
    $(".subpages_container .sp_title span").click(function(){
        id=$(this).attr("id").replace("close-",'');
        $("#subpages-"+id).hide();
    });
    
    $(".field_module_names").click(function(){
        id = $(this).attr("id").replace("modlabel-","");
        if($("#subpages-"+id).is(":visible")) return;
        $(".subpages-container:visible").hide();
        $("#subpages-"+id).show();
    });
    


    $(".modules-field a").click(function(){

        var theParent = $(this).parent().parent().parent().parent().attr("id");
        var id = $(this).parent().children("input").val();

        if($(".subpages-"+id).is(":visible"))
            return false;

        $("#"+theParent+" .subpages-container").hide();

        if($(".subpages-"+id).length<=0)
            $("#"+theParent+" > div:last-child h4").html('');

        $(".subpages-"+id).fadeIn('fast').parent().children('h4').html(cuLanguage.modulePages.replace("%s", $(this).html()));

    });


    $(".modules-field input[value='0']").each(function(){

        if($(this).attr("checked")!=undefined)
            $(this).parent().children("input").attr("checked", 'checked');

    });

    
});

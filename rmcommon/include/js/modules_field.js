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
    
    $(".modules-field input[type='checkbox']").click(function(){
        
        var id = $(this).val();
        var theParent = $(this).parent().parent().parent().parent().attr("id");

        if(id==0){
            if($(this).attr("checked")!=undefined)
                $("#"+theParent+" li > input").attr("checked",  'checked');
            else
                $("#"+theParent+" li > input").removeAttr("checked");
            return;
        }

        if ($(this).is(":checked")){
            $("#"+theParent+" .subpages-"+id+" input").attr("checked", 'checked');
        } else {
            $("#"+theParent+" .subpages-"+id+" input").removeAttr("checked");
        }

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

    $(".subpages-container input").click(function(){

        var obj = $(this).parent().parent();

        var checks = obj.children("li").children("input[checked='checked']").length;
        var id = $(obj).parent().parent().attr("id");

        if(checks<=0)
            $("#"+id+" input[value='"+obj.data("module")+"']").removeAttr("checked");

        if(checks>0 && !$("#"+id+" input[value='"+obj.data("module")+"']").is(":checked"))
            $("#"+id+" input[value='"+obj.data("module")+"']").attr("checked", "checked");

    });

    $(".modules-field input[value='0']").each(function(){

        if($(this).attr("checked")!=undefined)
            $(this).parent().children("input").attr("checked", 'checked');

    });

    
});

x.add_plugin('dropdown',{
    init : function(x){
        $("head").append('<link rel="stylesheet" type="text/css" href="'+x.editor_path+'/plugins/dropdown/css/dropdowns.css" />');
        $("#"+x.ed).hover(function(){
            $("#"+x.ed+'-ed-container .dropdown_menu').hide();
        });
    },
    add_dropdown: function(x, d){

        var dd = '<ul class="dropdown_menu dropdown_'+d.name+'" style="width: '+(d.width!=undefined?d.width:'')+'; height: '+(d.height!=undefined?d.height:'')+';">'+d.content+'</ul>';
        $("#"+x.ed+'-ed-container').append(dd);
        
    },
    show_menu: function(x,n){
        name = '#'+x.ed+'-'+n+' .dropdown_menu';
        if($(name).length<=0){
            $('#'+x.ed+'-'+n).append('<ul class="dropdown_menu" style="width: '+$('#'+x.ed+'-ed-container .dropdown_'+n).css('width')+'; height: '+$('#'+x.ed+'-ed-container .dropdown_'+n).css('height')+';">'+$('#'+x.ed+'-ed-container .dropdown_'+n).html()+'</ul>');
            $('#'+x.ed+'-ed-container .dropdown_'+n).remove();
        }
        
        if ($(name).is(':visible')){
            $(name).hide();
            return;
        }
        
        $('#'+x.ed+'-'+n).hover(function(){},function(){
            $(name).hide();
        });
        
        $(name).slideDown(100);
    },
    hide_menu: function(n){
        name = '#'+x.ed+'-'+n+' .dropdown_menu';
        $(name).slideUp(100);
    }
});

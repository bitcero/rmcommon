x.add_plugin('fonts',{
    init: function(x){
        
        var sizes = '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'xx-small\');" style="font-size:xx-small;"><?php _e('Extra Pequeña','rmcommon'); ?></a></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'x-small\');" style="font-size:X-Small;"><?php _e('Muy Pequeña','rmcommon'); ?></a></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'small\');" style="font-size: small;"><?php _e('Pequeña','rmcommon'); ?></a></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'medium\');" style="font-size: medium;"><?php _e('Mediana','rmcommon'); ?></a></li></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'large\');" style="font-size: large;"><?php _e('Grande','rmcommon'); ?></a></li></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'x-large\');" style="font-size: x-large;"><?php _e('Muy Grande','rmcommon'); ?></a></li></li>';
        sizes += '<li><a href="javascript:;" onclick="'+x.name+'.fonts.insert(\'xx-large\');" style="font-size: xx-large;"><?php _e('Extra Grande','rmcommon'); ?></a></li></li>';
        
        x.dropdown.add_dropdown(x,{
            name : 'fontsize',
            content : sizes,
            width: '200px'
        });
        
        var fonts = '<li style="font-family: Arial;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Arial</a></li>';
        fonts += '<li style="font-family: Courier;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Courier</a></li>';
        fonts += '<li style="font-family: Georgia;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Georgia</a></li>';
        fonts += '<li style="font-family: Helvetica;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Helvetica</a></li>';
        fonts += '<li style="font-family: Impact;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Impact</a></li>';
        fonts += '<li style="font-family: Verdana;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Verdana</a></li>';
        fonts += '<li style="font-family: Haettenschweiler;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Haettenschweiler</a></li>';
        fonts += '<li style="font-family:Verdana, Geneva, sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Verdana, Geneva, sans-serif</a></li>';
        fonts += '<li style="font-family: Georgia, \'Times New Roman\', Times, serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Georgia, Times New Roman, Times, serif</a></li>';
        fonts += '<li style="font-family: \'Courier New\', Courier, monospace;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Courier New, Courier, monospace</a></li>';
        fonts += '<li style="font-family: Arial, Helvetica, sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Arial, Helvetica, sans-serif</a></li>';
        fonts += '<li style="font-family: Tahoma, Geneva, sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Tahoma, Geneva, sans-serif</a></li>';
        fonts += '<li style="font-family: \'Trebuchet MS\', Arial, Helvetica, sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Trebuchet MS, Arial, Helvetica, sans-serif</a></li>';
        fonts += '<li style="font-family: \'Arial Black\', Gadget, sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Arial Black, Gadget, sans-serif</a></li>';
        fonts += '<li style="font-family: \'Palatino Linotype\', \'Book Antiqua\', Palatino, serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Palatino Linotype, Book Antiqua, Palatino, serif</a></li>';
        fonts += '<li style="font-family: \'Lucida Sans Unicode\', \'Lucida Grande\', sans-serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Lucida Sans Unicode, Lucida Grande, sans-serif</a></li>';
        fonts += '<li style="font-family: \'MS Serif\', \'New York\', serif;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">MS Serif, New York, serif</a></li>';
        fonts += '<li style="font-family: \'Lucida Console\', Monaco, monospace;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Lucida Console, Monaco, monospace</a></li>';
        fonts += '<li style="font-family: \'Comic Sans MS\', cursive;"><a href="javascript:;" onclick="'+x.name+'.fonts.insert_font(this);">Comic Sans MS, cursive</a></li>';
        
        x.dropdown.add_dropdown(x, {
            name: 'font',
            content: fonts,
            width: '300px',
            height: '300px'
        });
    },
    show: function(x){
        x.dropdown.show_menu(x, 'fontsize');
    },
    show_font : function(){
        x.dropdown.show_menu(x, 'font');
    },
    insert: function(size){
        x.insertText('[size='+size+']%replace%[/size]');
        x.dropdown.hide_menu('fontsize');
    },
    insert_font: function(a){
        x.insertText('[font='+$(a).css('font-family')+']%replace%[/font]');
        x.dropdown.hide_menu('font');
        
    }
});

x.add_button('fontsize',{
   name : 'fontsize', 
   title : 'Font Size',
   alt : 'Font Size',
   cmd : 'show',
   plugin : 'fonts',
   row: 'top',
   type : 'dropdown',
   icon : x.editor_path+'/images/size.png'
});
x.add_button('font',{
   name : 'font', 
   title : 'Font Family',
   alt : 'Font Family',
   cmd : 'show_font',
   plugin : 'fonts',
   row: 'top',
   type : 'dropdown',
   text : 'Select font...'
});
x.add_button('fontcolor',{
    name : 'fontcolor', 
   title : 'Font color',
   alt : 'Font color',
   cmd : function(){
       $("#"+x.ed+"-ec-container #"+x.ed+"-fontcolor").ColorPicker({
            onSubmit: function(hsb, hex, rgb, el){
                x.insertText("[color="+hex+"]%replace%[/color]");
                $(el).ColorPickerHide();
            }
       });
   },
   plugin : 'fonts',
   row: 'top',
   type: 'dropdown',
   cmd_type: 'auto',
   icon: x.editor_path+'/images/color.png'
});

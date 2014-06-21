x.add_plugin('chars', {
    show: function(){
        x.popup({
            width: 620,
            height: 310,
            title: 'Insert Custom Character',
            url: x.url+'/plugins/chars/chars.html'
        });
    }
});

x.add_button('chars',{
   name : 'chars', 
   title : 'Insert custom character',
   alt : 'Symbols',
   cmd : 'show',
   plugin : 'chars',
   row: 'bottom',
   icon: x.editor_path+'/images/symbol.png'
});
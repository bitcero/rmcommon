x.add_plugin('link', {
    show: function(){
        x.popup({
            width: 400,
            height: 250,
            title: 'Insert Link',
            url: x.url+'/plugins/link/links.html'
        });
    },
    insert: function(){
        
    }
});

x.add_button('link',{
   name : 'link', 
   title : 'Insert link',
   alt : 'Insert link',
   cmd : 'show',
   plugin : 'link',
   row: 'bottom',
   icon: x.editor_path+'/images/link.png'
});
x.add_plugin('xquote', {
    show: function(){
    	
        x.popup({
            width: 350,
            height: 250,
            title: 'Insert Quote',
            url: x.url+'/plugins/xquote/quote.html'
        });
        
    },
    check: function(s){
        
    }
});

x.add_button('xquote',{
   name : 'xquote', 
   title : 'Insert quote',
   alt : 'Quote',
   cmd : 'show',
   plugin : 'xquote',
   row: 'bottom',
   icon: x.editor_path+'/images/quote.png'
});
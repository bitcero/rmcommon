x.add_plugin('xcode', {
    show: function(){
    	
        x.popup({
            width: 350,
            height: 250,
            title: 'Insert Code Block',
            url: x.url+'/plugins/xcode/code.html'
        });
        
    },
    check: function(s){
        
    }
});

x.add_button('xcode',{
   name : 'xcode', 
   title : 'Insert code block',
   alt : 'Code',
   cmd : 'show',
   plugin : 'xcode',
   row: 'bottom',
   icon: x.editor_path+'/images/code.png'
});
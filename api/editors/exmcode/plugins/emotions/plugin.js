x.add_plugin('emotions', {
    show: function(){
    	
        x.popup({
            width: 300,
            height: 300,
            title: 'Emotions Icons',
            url: x.url+'/plugins/emotions/emotions.php'
        });
        
    },
    check: function(s){
        
    }
});

x.add_button('emotions',{
   name : 'emotions', 
   title : 'Insert Emotions',
   alt : 'Emotions',
   cmd : 'show',
   plugin : 'emotions',
   row: 'bottom',
   icon: x.editor_path+'/images/smiley.png'
});
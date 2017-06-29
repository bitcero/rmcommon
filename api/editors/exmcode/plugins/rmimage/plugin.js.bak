x.add_plugin('rmimage', {
    show: function(){
    	
        x.popup({
            width: 600,
            height: 600,
            title: 'Image Manager',
            url: '<?php echo RMCURL; ?>/include/tiny-images.php',
            single: 1,
            maximizable: 1
        });
        
    },
    check: function(s){
        
    }
});

x.add_button('images',{
   name : 'images', 
   title : 'Image Manager',
   alt : 'Image',
   cmd : 'show',
   plugin : 'rmimage',
   row: 'bottom',
   icon: x.editor_path+'/images/images.png'
});
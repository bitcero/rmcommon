x.add_plugin('more', {
    more: function(){
    	
    	x.insertText('<!--more-->');
        
    },
    page: function(){
        x.insertText('<!--nextpage-->');
    }
});

x.add_button('more',{
   name : 'more', 
   title : 'Insert more break',
   alt : 'More',
   cmd : 'more',
   plugin : 'more',
   row: 'top',
   icon: x.editor_path+'/images/more.gif'
});

x.add_button('page',{
   name : 'page', 
   title : 'Insert next page break',
   alt : 'Next Page',
   cmd : 'page',
   plugin : 'more',
   row: 'bottom',
   icon: x.editor_path+'/images/page.gif'
});
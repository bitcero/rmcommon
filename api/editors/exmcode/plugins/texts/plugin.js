x.add_plugin('texts', {
    insert : function(t,x){
        x.insertText('['+t+']%replace%[/'+t+']');
    },
    bold : function(x){
        this.insert('b',x);
    },
    italic : function(x){
        this.insert('i',x);
    },
    underline : function(x){
        this.insert('u',x);
    },
    strikeout : function(x){
        this.insert('d',x);
    },
    left: function(x){
        this.insert('left',x);
    },
    center : function(x){
        this.insert('center',x);
    },
    right : function(x){
        this.insert('right',x);
    }
});

x.add_button('bold',{
   name : 'bold', 
   title : 'Bold',
   alt : 'Bold',
   icon : x.editor_path+'/images/bold.png',
   cmd : 'bold',
   plugin : 'texts',
   row: 'top'
});
x.add_button('italic',{
   name : 'italic', 
   title : 'Italic',
   alt : 'Italic',
   icon : x.editor_path+'/images/italic.png',
   cmd : 'italic',
   plugin : 'texts',
   row: 'top'
});
x.add_button('underline',{
   name : 'underline', 
   title : 'Underline',
   alt : 'Underline',
   icon : x.editor_path+'/images/under.png',
   cmd : 'underline',
   plugin : 'texts',
   row: 'top'
});
x.add_button('strikeout',{
   name : 'strikeout', 
   title : 'Strikeout',
   alt : 'Strikeout',
   icon : x.editor_path+'/images/strike.png',
   cmd : 'strikeout',
   plugin : 'texts',
   row: 'top'
});

x.add_button('left',{
   name : 'left', 
   title : 'Align to left',
   alt : 'Align to left',
   icon : x.editor_path+'/images/left.png',
   cmd : 'left',
   plugin : 'texts',
   row: 'top'
});
x.add_button('center',{
   name : 'center', 
   title : 'Align to center',
   alt : 'Align to center',
   icon : x.editor_path+'/images/center.png',
   cmd : 'center',
   plugin : 'texts',
   row: 'top'
});
x.add_button('right',{
   name : 'right', 
   title : 'Align to right',
   alt : 'Align to right',
   icon : x.editor_path+'/images/right.png',
   cmd : 'right',
   plugin : 'texts',
   row: 'top'
});
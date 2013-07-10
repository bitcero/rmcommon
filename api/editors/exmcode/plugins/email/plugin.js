x.add_plugin('email', {
    show: function(){
    	
        var sel = x.selection();
        if(x.email.check(sel.text)){
			x.insertText('[email]%replace%[/email]');
			return;
        }
        
        var email = prompt('<?php _e('Input the email address to insert:','rmcommon'); ?>');
        if (!email) return;
        if (!x.email.check(email)){
			alert(email+' <?php _e('nos not a valid email address!','rmcommon'); ?>');
			return;
        }
        x.insertText('[email]'+email+'[/email]');
        
    },
    check: function(s){
        if (s.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
			return true;
		else
			return false;
    }
});

x.add_button('email',{
   name : 'email',
   title : 'Insert email',
   alt : 'Insert email',
   cmd : 'show',
   plugin : 'email',
   row: 'bottom',
   icon: x.editor_path+'/images/mail.png'
});
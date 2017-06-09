var switchEditors = {

	mode : '',

	I : function(e) {
		return document.getElementById(e);
	},

	edInit : function() {
		//var h = tinymce.util.Cookie.getHash("TinyMCE_content_size"),
		var h = $.cookie("editor"),
            H = this.I('edButtonHTML'),
            P = this.I('edButtonPreview');

		// Activate TinyMCE if it's the user's default editor
        try {
            this.I("quicktags").style.display = "none";
        } catch(e){};
        tinymce.execCommand("mceAddControl", false, "content");

        
	},

	saveCallback : function(el, content, body) {

		if ( tinymce.activeEditor.isHidden() )
			content = this.I(el).value;
		else
			content = this.pre_esautop(content);

		return content;
	},

	pre_esautop : function(content) {
		// We have a TON of cleanup to do. Line breaks are already stripped.

		// Protect pre|script tags
		content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
			a = a.replace(/<br ?\/?>[\r\n]*/g, '<es_temp>');
			return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '<es_temp>');
		});

		// Pretty it up for the source editor
		var blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|p';
		content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('\\s*<(('+blocklist1+')[^>]*)>', 'mg'), '\n<$1>');

		// Mark </p> if it has any attributes.
		content = content.replace(new RegExp('(<p [^>]+>.*?)</p>', 'mg'), '$1</p#>');

		// Sepatate <div> containing <p>
		content = content.replace(new RegExp('<div([^>]*)>\\s*<p>', 'mgi'), '<div$1>\n\n');

		// Remove <p> and <br />
		content = content.replace(new RegExp('\\s*<p>', 'mgi'), '');
		content = content.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

		// Fix some block element newline issues
		content = content.replace(new RegExp('\\s*<div', 'mg'), '\n<div');
		content = content.replace(new RegExp('</div>\\s*', 'mg'), '</div>\n');
		content = content.replace(new RegExp('\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*', 'gi'), '\n\n[caption$1[/caption]\n\n');
		content = content.replace(new RegExp('caption\\]\\n\\n+\\[caption', 'g'), 'caption]\n\n[caption');

		var blocklist2 = 'blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre';
		content = content.replace(new RegExp('\\s*<(('+blocklist2+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
		content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'mg'), '</$1>\n');
		content = content.replace(new RegExp('<li([^>]*)>', 'g'), '\t<li$1>');

		if ( content.indexOf('<object') != -1 ) {
			content = content.replace(new RegExp('\\s*<param([^>]*)>\\s*', 'mg'), "<param$1>");
			content = content.replace(new RegExp('\\s*</embed>\\s*', 'mg'), '</embed>');
		}

		// Unmark special paragraph closing tags
		content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
		content = content.replace(new RegExp('\\s*(<p [^>]+>.*</p>)', 'mg'), '\n$1');

		// Trim whitespace
		content = content.replace(new RegExp('^\\s*', ''), '');
		content = content.replace(new RegExp('[\\s\\u00a0]*$', ''), '');

		// put back the line breaks in pre|script
		content = content.replace(/<es_temp>/g, '\n');

		// Hope.
		return content;
	},

	go : function(id, mode) {
        id = id || 'content';
		id = id.replace('#', '');

		mode = mode || this.mode || 'tinymce';
		var ed = tinymce.get(id) || false;
		var qt = $('#ed-cont-'+id+' .quicktags');
		var H = $('#ed-cont-'+id+' .edButtonHTML');
		var P = $('#ed-cont-'+id+' .edButtonPreview');
		var ta = $("#"+id);

		if ( 'tinymce' == mode ) {

			if ( ed && ! ed.isHidden() )
				return false;

			this.mode = 'html';
			ta.css('color','#fff');

			P.addClass('active');
			H.removeClass('active');
			edCloseAllTags(id); // :-(

			qt.hide().parent().removeClass("showing");

			//$(ta).val(this.esautop($(ta).val()));

			if ( ed ) ed.show();
			else tinymce.execCommand("mceAddControl", false, id);

            $.cookie('editor', 'tinymce');

		} else {
			if ( ! ed || ed.isHidden() )
				return false;

			this.mode = 'tinymce';
			H.addClass('active');
			P.removeClass('active');

			ta.css('height', ed.getContentAreaContainer().offsetHeight + 6 + 'px');
			$(ta).removeAttr('aria-hidden');

			ed.hide();
			qt.css('display','block').parent().addClass("showing");
            //qt.css('width', ta.css('width'));

			ta.css('color','');
            $.cookie('editor', 'html');

		}
		return false;
	},

	esautop : function(pee) {

		var blocklist = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]';

		pee = pee + "\n\n";
		pee = pee.replace(new RegExp('<br />\\s*<br />', 'gi'), "\n\n");
		pee = pee.replace(new RegExp('(<(?:'+blocklist+')[^>]*>)', 'gi'), "\n$1");
		pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), "$1\n\n");
		pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
		pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
		pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
		pee = pee.replace(new RegExp('<p>\\s*?</p>', 'gi'), '');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
		pee = pee.replace(new RegExp('<p>\\s*<blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
		pee = pee.replace(new RegExp('</blockquote>\\s*</p>', 'gi'), '</p></blockquote>');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)', 'gi'), "$1");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(new RegExp('\\s*\\n', 'gi'), "<br />\n");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
		pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
		pee = pee.replace(new RegExp('(?:<p>|<br ?/?>)*\\s*\\[caption([^\\[]+)\\[/caption\\]\\s*(?:</p>|<br ?/?>)*', 'gi'), '[caption$1[/caption]');
		// pee = pee.replace(new RegExp('^((?:&nbsp;)*)\\s', 'mg'), '$1&nbsp;');

		// Fix the pre|script tags
		pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
			a = a.replace(/<br ?\/?>[\r\n]*/g, '\n');
			return a.replace(/<\/?p( [^>]*)?>[\r\n]*/g, '\n');
		});

		return pee;
	}
};


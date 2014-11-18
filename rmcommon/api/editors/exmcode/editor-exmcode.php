/**
* $Id$
* ExmCode Editor
* Author: Eduardo Cortés <i.bitcero@gmail.com>
* http://eduardocortes.mx
*/
<?php
    require '../../../../../mainfile.php';
    XoopsLogger::getInstance()->activated = false;
    XoopsLogger::getInstance()->renderingEnabled = false;
    $lang = is_file(ABSPATH.'/api/editors/exmcode/language/'.EXMLANG.'.js') ? EXMLANG : 'en_US';
    $id = rmc_server_var($_GET, 'id', '');
?>

/*
 * jQuery plugin: fieldSelection - v0.1.0 - last change: 2006-12-16
 * (c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 */
(function() {

	var fieldSelection = {

		getSelection: function() {

			var e = this.jquery ? this[0] : this;

			return (

				/* mozilla / dom 3.0 */
				('selectionStart' in e && function() {
					var l = e.selectionEnd - e.selectionStart;
					return { start: e.selectionStart, end: e.selectionEnd, length: l, text: e.value.substr(e.selectionStart, l) };
				}) ||

				/* exploder */
				(document.selection && function() {

					e.focus();

					var r = document.selection.createRange();
					if (r == null) {
						return { start: 0, end: e.value.length, length: 0 }
					}

					var re = e.createTextRange();
					var rc = re.duplicate();
					re.moveToBookmark(r.getBookmark());
					rc.setEndPoint('EndToStart', re);

					return { start: rc.text.length, end: rc.text.length + r.text.length, length: r.text.length, text: r.text };
				}) ||

				/* browser not supported */
				function() {
					return { start: 0, end: e.value.length, length: 0 };
				}

			)();

		},

		replaceSelection: function() {

			var e = this.jquery ? this[0] : this;
			var text = arguments[0] || '';

			return (

				/* mozilla / dom 3.0 */
				('selectionStart' in e && function() {
					e.value = e.value.substr(0, e.selectionStart) + text + e.value.substr(e.selectionEnd, e.value.length);
					return this;
				}) ||

				/* exploder */
				(document.selection && function() {
					e.focus();
					document.selection.createRange().text = text;
					return this;
				}) ||

				/* browser not supported */
				function() {
					e.value += text;
					return this;
				}

			)();

		}

	};

	jQuery.each(fieldSelection, function(i) { jQuery.fn[i] = this; });

})();


// Make the editor accesible from any location
var editor_path = '';
var top_buttons = '';
var bottom_buttons = '';

var exmCode<?php echo ucfirst($id); ?> = {
    // Init
	init: function(path, lang){
        var x = this;
        x.url = '<?php echo RMCURL.'/api/editors/exmcode'; ?>';
        x.editor_path = x.url;
        x.ed = '<?php echo $id; ?>';
        x.name = 'exmCode<?php echo ucfirst($id); ?>';
        // Add plugins
        <?php
        // Cargamos los plugins
        $path = RMCPATH.'/api/editors/exmcode/plugins';
        $dir = opendir($path);
        while (FALSE !== ($file = readdir($dir))) {
            if ($file=='.' || $file=='..') continue;
            if (!is_dir($path.'/'.$file)) continue;
            if (!is_file($path.'/'.$file.'/plugin.js')) continue;

            include $path.'/'.$file.'/plugin.js';

        }

        // New plugins from other components
        RMEvents::get()->run_event('rmcommon.load.exmcode.plugins', $id);

        ?>

        //x.add_separator('top');
        x.add_button('bottom', {
            name : 'bottom',
            title : 'Show bottom toolbar',
            alt : 'Bottom toolbar',
            icon : x.editor_path+'/images/show.png',
            row : 'top',
            cmd : function(x){
                $("#<?php echo $id; ?>-ec-container .row_bottom").slideDown('fast');
                $("#<?php echo $id; ?>-bottom").hide();
            },
            cmd_type : 'auto'
        });
        x.add_button('top', {
            name : 'top',
            title : 'Hide toolbar',
            alt : 'Hide toolbar',
            icon : x.editor_path+'/images/hide.png',
            row : 'bottom',
            cmd : function(x){
                $("#<?php echo $id; ?>-ec-container .row_bottom").slideUp('fast');
                $("#<?php echo $id; ?>-bottom").show();
            },
            cmd_type : 'auto'
        });
        //x.add_separator('bottom');

        var buttons = new Array();
        buttons = buttons.concat(<?php echo $id; ?>_buttons.split(','));
        plugs = <?php echo $id; ?>_plugins;

        for (i=0;i<buttons.length;i++) {

            buttons[i] = buttons[i].replace(/^\s*|\s*$/g,"");

            if (buttons[i]=='separator_t' || buttons[i]=='separator_b') {
                x.add_separator(buttons[i]=='separator_t'?'top':'bottom');
                continue;
            }

            if (x.buttons[buttons[i]]==undefined) continue;
            d = x.buttons[buttons[i]];

            if (d.plugin!=undefined && plugs[d.plugin]==undefined) continue;

                var b = '<span class="buttons" id="<?php echo $id; ?>-'+d.name+'" accesskey="'+d.key+'" title="'+d.title+'" onclick="'+x.name+'.button_press(\''+d.name+'\');">';
                b += "<span>";
                if (d.icon!=undefined) {
                    b += "<img src='"+d.icon+"' alt='' />";
                }
                if (d.type=='dropdown') {
                    b += "<span class='dropdown'><img src='"+x.editor_path+"/images/down.png' alt='' /></span>";
                }
                b += (d.text!=undefined ? d.text : '')+"</span>";
                b += "</span>";

                if (d.row == 'top') {
                    $("#<?php echo $id; ?>-ec-container .row_top").append(b);
                } else {
                    $("#<?php echo $id; ?>-ec-container .row_bottom").append(b);
                }

        }

	},
    add_button : function(n,d){

        var x = this;
        x.buttons = x.buttons || {};
        x.buttons[n] = d;

    },
    add_separator : function(w){
        if (w == 'top') {
            $("#<?php echo $id; ?>-ec-container .row_top").append('<span class="separator"></span>');
        } else {
            $("#<?php echo $id; ?>-ec-container .row_bottom").append('<span class="separator"></span>');
        }
    },
    add_plugin : function(n,d){
        var x = this;
        if (x[n]!=undefined) return;

        plugs = <?php echo $id; ?>_plugins;
        if (plugs[n]==undefined) return;

        x[n] = d;

        if (x[n].init!=undefined) x[n].init(x);

    },
    button_press : function(n){
        var x = this;
        if (x.buttons[n]=='undefined') return;

        if (x.buttons[n].cmd_type=='auto') {
            x.buttons[n].cmd(x);

            return;
        }

        plugin = x.buttons[n].plugin;
        x.command(plugin, x.buttons[n].cmd);

    },
    command : function(n, c, p){
        var x = this;

        if (x[n]==undefined) return;

        eval("x."+n+"."+c+"(x,p)");
    },
    execute : function(c){
        x = this;
        eval('x.'+c);
    },
    selection: function(){
    	x = this;

        return $("#"+x.ed).getSelection();
    },
    insertText: function(what){
        var x = this;
        var e = document.getElementById(x.ed);
        var scrollTop = e.scrollTop;
        selected = $("#"+x.ed).getSelection();
        if(selected.text==null)
            selected.text = '';

        text = what.replace('%replace%', selected.text);
        $("#"+x.ed).replaceSelection(text, true);

        var cursorPos = 0;
        if (selected.text=='') {
            cursorPos = what.indexOf("%replace%");
            cursorPos = e.selectionStart + (cursorPos < 0 ? text.length : cursorPos);
            e.selectionStart = cursorPos;
        } else {
            cursorPos = selected.start + text.length;
        }

        //cursorPos = cursorPos<0 || cursorPos<=selected.start ? (selected.start + text.length) : cursorPos;

        e.selectionEnd = cursorPos;
        e.scrollTop = scrollTop;
        $("#"+x.ed).focus();

    },
    popup : function(d){
        x = this;
        if ($("#"+x.ed+"-ed-container .popup").length<=0) {
            var pop = '<div class="popblocker"></div><div class="popup">';
            pop += '<div class="titlebar window-title cu-titlebar">';
            pop += '<span class="buttons">';
            pop += '<span class="close" onclick="'+x.name+'.closePopup()"><span>Close</span></span>';
            pop += '<span class="maximize" onclick="'+x.name+'.maximize()"><span>Maximize</span></span>';
            pop += '<span class="restore"><span>Restore</span></span>';
            pop += '</span>';
            pop += '<span class="title"></span></div>';
            pop += '<div class="content"><iframe src=""></iframe></div></div>';
            $("#"+x.ed+"-ed-container").append(pop);
        }
        var pn = "#"+x.ed+"-ed-container .popup";
        var w = d.width!=undefined?d.width:300;
        var h = d.height!=undefined?d.height:300;
        $(pn).css('width', w+'px');
        $(pn).css('height', h+'px');
        $(pn).css({
            top: '50%',
            left: '50%',
            'margin-left': '-'+(w/2)+'px',
            'margin-top': '-'+(h/2)+'px',
            'position': 'fixed'
        });
        $(pn+' .title').html(d.title!=undefined?d.title:'');
        $(pn+' .content').css('height',(h-39)+'px');

        if (!d.single) {
        	d.url = encodeURIComponent(d.url).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
        	var url = encodeURIComponent(x.url).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');

        	var params = '&id='+x.ed+'&name='+x.name+'&eurl='+url+'&lang=<?php echo _LANGCODE; ?>';
        	params += '&theme=<?php echo $rmc_config['theme']; ?>';
        	params += '&version=<?php echo RMCVERSION; ?>';
        	$(pn+' iframe').attr('src', x.url+'/urls.php?url='+d.url+params);
        } else {

            var con = d.url.indexOf('?') != -1 ? '&' : '?';

        	$(pn+' iframe').attr('src', d.url+con+'type=exmcode&name='+x.name);
        }
        if (d.maximizable!=undefined&&d.maximizable) $(pn+' .maximize').show();

        $("#"+x.ed+"-ed-container .popblocker").show(10, function(){
            $(pn).show();
        });

        w = $(pn).width();
        h = $(pn).height();

        $(pn+' .restore').click(function(){
            x.restore(w,h);
        });
    },
    closePopup: function(){
        x = this;
        var pn = "#"+x.ed+"-ed-container .popup";
        $(pn+' iframe').attr('src', '');
        $("#"+x.ed+"-ed-container .popup").hide();
        $("#"+x.ed+"-ed-container .popblocker").hide();
    },
    maximize: function(){
        x = this;
        var pn = "#"+x.ed+"-ed-container .popup";
        $(pn+' .maximize').hide();
        $(pn+' .restore').show();

        $(pn).animate({
            'width': '100%',
            'height': '100%',
            'left': 0,
            'top': 0,
            'margin': 0
        }, 500, '', function(){
            $(pn+' .content').css('height',$(window).height()-39+'px');
        });
        $(pn).addClass('maximized');

    },
    restore: function(w,h){
        x = this;
        var pn = "#"+x.ed+"-ed-container .popup";
        $(pn+' .maximize').show();
        $(pn+' .restore').hide();

        $(pn).animate({
            'width': w+'px',
            'height': h+'px',
            'left': '50%',
            'top': '50%',
        }, 500, '', function(){
            $(pn+' .content').css('height',(h-39)+'px');
            $(pn).css({'margin-left': '-'+(w/2)+'px',
            'margin-top': '-'+(h/2)+'px', 'display':'fixed'});
        });
        $(pn).removeClass('maximized');
    }
};

$(document).ready(function(){
	exmCode<?php echo ucfirst($id); ?>.init();
	//exmCode.make_buttons('<?php echo rmc_server_var($_GET, 'id'); ?>');
});

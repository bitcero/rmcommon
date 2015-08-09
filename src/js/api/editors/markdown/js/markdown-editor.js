/*!
MarkDown editor for Common Utilities
http://rmcommon.com
author: Eduardo Cortés <i.bitcero@gmail.com>
Website: http://eduardocortes.mx
This component is part of Common Utilities
 */

//(function(){

    var mdEditor = {

        editors: {},
        buttons: {},
        callbacks: {},
        plugins: {},

        btn_tpl: '<button type="button" class="button" data-id="%id" data-editor="%e" accesskey="%k" title="%t">%c</button>',
        menu_item_tpl: '<button type="button" class="" data-id="%id" data-owner="%ow" data-editor="%e" accesskey="%k">%i %c</button>',
        btn_menu_tpl: '<div class="btn-group"><button type="button" class="button dropdown-toggle" data-toggle="dropdown" data-id="%id" data-editor="%e" accesskey="%k" title="%t">%c</button>%m</div>',
        menu_tpl: '<ul class="dropdown-menu" role="menu">%li</ul>',

        init: function( id, options ){

            this.editors[id] = this.get( id, options );
            this.render_buttons(id);
            this.listeners();

        },

        get: function( id, options ){

            if ( undefined == id || '' == id )
                return false;

            return CodeMirror.fromTextArea( document.getElementById(id), options );
        },

        add_button: function( data ){

            if ( undefined == data.id || data.id == '' )
                return false;

            if ( undefined != data.type && 'menu' == data.type ){
                // Menu owner
                this.buttons[data.id] = {
                    caption: data.caption,
                    id: data.id,
                    callback: data.callback,
                    icon: undefined != data.icon ? data.icon : '',
                    key: data.key,
                    content: data.content,
                    default: undefined != data.default ? data.default : '',
                    type: undefined != data.type ? data.type : 'button',
                    menu: {}
                };

            } else if ( undefined != data.owner && '' != data.owner ){

                this.buttons[data.owner].menu[data.id] = {
                    caption: data.caption,
                    id: data.id,
                    owner: data.owner,
                    callback: data.callback,
                    icon: undefined != data.icon ? data.icon : '',
                    key: data.key,
                    content: data.content,
                    default: undefined != data.default ? data.default : '',
                };

            } else {

                this.buttons[data.id] = {
                    caption: data.caption,
                    id: data.id,
                    callback: data.callback,
                    icon: undefined != data.icon ? data.icon : '',
                    key: data.key,
                    content: data.content,
                    default: undefined != data.default ? data.default : '',
                    type: undefined != data.type ? data.type : 'button'
                };

            }


        },

        save: function( id ){
            this.editors[id].save();
        },

        add_callback: function( id, callback ){

            this.callbacks[id] = callback;

        },

        render_buttons: function( id ){

            $toolbar = $("#" + id + "-buttons-container > .toolbar-1");
            var menu_tpl = '<li>'+this.menu_tpl+'</li>';

            for(var i in this.buttons){
                var btn = this.buttons[i];
                if ( undefined != this.buttons[i].menu )
                    var tpl = this.btn_menu_tpl;
                else
                    var tpl = this.btn_tpl;
                tpl = tpl.replace('%id', btn.id);
                tpl = tpl.replace('%k', btn.key);
                tpl = tpl.replace('%e', id);
                if ( '' != btn.icon ) {
                    tpl = tpl.replace('%t', btn.caption);
                    tpl = tpl.replace('%c', '<span class="' + btn.icon + '"></span>');
                } else {
                    tpl = tpl.replace('%c', btn.caption);
                }

                // Check menu
                if ( undefined != this.buttons[i].menu ){
                    var menu = '';
                    for( var m in this.buttons[i].menu ){
                        var mbtn = btn.menu[m];
                        var menu_button_tpl = '<li>'+this.menu_item_tpl+'</li>';
                        menu_button_tpl = menu_button_tpl.replace('%id', mbtn.id );
                        menu_button_tpl = menu_button_tpl.replace('%ow', mbtn.owner );
                        menu_button_tpl = menu_button_tpl.replace('%k', mbtn.key);
                        menu_button_tpl = menu_button_tpl.replace('%e', id);
                        if ( '' != btn.icon )
                            menu_button_tpl = menu_button_tpl.replace('%i', '<span class="' + mbtn.icon + '"></span>');
                        else
                            menu_button_tpl = menu_button_tpl.replace('%i', '');

                        menu_button_tpl = menu_button_tpl.replace('%c', mbtn.caption);
                        menu += menu_button_tpl;
                    }
                    tpl = tpl.replace('%m', this.menu_tpl.replace('%li', menu) );
                }

                $toolbar.append(tpl);
            }

        },

        listeners: function(){

            var ed = this;

            $(".ed-container > .ed-buttons > .toolbar-1  button").click(function() {

                var id = $(this).data('id');
                var owner = $(this).data('owner');

                if (undefined == id || '' == id)
                    return;

                if ( undefined != owner && '' != owner ){

                    if (undefined == ed.buttons[owner])
                        return;

                    var btn = ed.buttons[owner].menu[id];

                } else {

                    if (undefined == ed.buttons[id])
                        return;

                    var btn = ed.buttons[id];

                }

                var editor = ed.editors[$(this).data('editor')];

                if( 'function' == typeof btn.callback ) {

                    btn.callback(editor, ed, id);

                } else if( 'string' == typeof btn.callback && '' != btn.callback ){

                    var cbk = btn.callback;

                    if ( 'function' == typeof ed.callbacks[cbk] )
                        ed.callbacks[cbk](editor, ed, id);

                } else if ( 'string' == typeof btn.content ) {

                    ed.insert( $(this).data('editor'), btn.content, btn.default );

                }

            });

        },

        insert: function( id, content, def ){

            var editor = this.editors[id];
            var current = editor.getSelection();
            var pos = editor.getCursor();

            if ( '' == current && '' != def )
                current = def;

            if ( '' == current )
                pos.ch += content.split('%')[0].length;

            editor.replaceSelection( content.replace("%", current) );

            if ('' == current )
                editor.setCursor( pos );

            editor.focus();

        },

        selection: function( id ){
            var editor = this.editors[id];
            var selection = {
                cursor: editor.getCursor(),
                text:   editor.getSelection()
            };
            return selection;
        },

        popup_form: function( id, content ){

            $("#" + id + "-ed-container .txtarea-container").append('<div id=')

        }

    };

//})();

(function(mdEditor){

    /*
     More Code
     */
    mdEditor.add_button({
        id: 'head',
        caption: 'Heading',
        icon: 'fa fa-header',
        key: 'h',
        callback: '',
        default: '',
        content: '',
        type: 'menu'
    });

    mdEditor.add_button({
        id: 'h1',
        owner: 'head',
        caption: '<h1>Heading 1</h1>',
        icon: '',
        key: '1',
        callback: '',
        default: '',
        content: '# %',
    });

    mdEditor.add_button({
        id: 'h2',
        owner: 'head',
        caption: '<h2>Heading 2</h2>',
        icon: '',
        key: '2',
        callback: '',
        default: '',
        content: '## %',
    });

    mdEditor.add_button({
        id: 'h3',
        owner: 'head',
        caption: '<h3>Heading 3</h3>',
        icon: '',
        key: '3',
        callback: '',
        default: '',
        content: '### %',
    });

    mdEditor.add_button({
        id: 'h4',
        owner: 'head',
        caption: '<h4>Heading 4</h4>',
        icon: '',
        key: '4',
        callback: '',
        default: '',
        content: '#### %',
    });

    mdEditor.add_button({
        id: 'h5',
        owner: 'head',
        caption: '<h5>Heading 5</h5>',
        icon: '',
        key: '5',
        callback: '',
        default: '',
        content: '##### %',
    });

    mdEditor.add_button({
        id: 'h6',
        owner: 'head',
        caption: '<h6>Heading 6</h6>',
        icon: '',
        key: '6',
        callback: '',
        default: '',
        content: '###### %',
    });



    mdEditor.add_button({
        id: 'bold',
        caption: 'Bold',
        icon: 'fa fa-bold',
        key: 'b',
        callback: '',
        content: '**%**'
    });

    mdEditor.add_button({
        id: 'italic',
        caption: 'Italic',
        icon: 'fa fa-italic',
        key: 'i',
        callback: '',
        content: '_%_'
    });

    mdEditor.add_button({
        id: 'strikethrough',
        caption: 'Strikethrough',
        icon: 'fa fa-strikethrough',
        key: 's',
        callback: '',
        content: '~~%~~'
    });

    /* Callback for Lists */
    mdEditor.add_callback( 'list', function(ed, mde, id){
        var selected = ed.getSelection();
        var cursor = ed.getCursor('from');
        var pre = '';
        var char = 'list'==id ? '*' : '1';

        if ( cursor.ch > 0 )
            pre = '\n\n';

        if ( selected.length > 0 ){

            var line = ed.getLine( cursor.line );
            if ( line.length > cursor.ch + selected.length )
                ed.replaceSelection( pre + char + ' ' + selected + '\n\n');
            else
                ed.replaceSelection( pre + char + ' ' + selected + '\n\n');

            cursor.line += cursor.ch>0 ? 2 : 0;
            cursor.ch = selected.length + 2;
            ed.setCursor( cursor );
            ed.focus();

        } else {

            char = '*'==char ? '* \n* \n* ' : '1. \n2. \n3. ';
            var line = ed.getLine( cursor.line );
            if ( line.length > cursor.ch )
                ed.replaceSelection(pre + char + ' \n\n');
            else
                ed.replaceSelection( pre + char );

            cursor.line += cursor.ch>0 ? 2 : 0;
            cursor.ch += 2;
            ed.setCursor( cursor );
            ed.focus();

        }

    });

    mdEditor.add_button({
        id: 'list',
        caption: 'Unorderd list',
        icon: 'fa fa-list-ul',
        key: 'u',
        callback: 'list',
        content: ''
    });

    mdEditor.add_button({
        id: 'ordered',
        caption: 'Unorderd list',
        icon: 'fa fa-list-ol',
        key: 'o',
        callback: 'list',
        content: ''
    });

    /*
    LINKS
     */
    mdEditor.add_button({
        id: 'link',
        caption: 'Insert Link',
        icon: 'fa fa-chain',
        key: 'l',
        callback: '',
        default: 'Link Caption',
        content: '[%](insert url here)'
    });

    /*
    IMAGES
     */
    mdEditor.add_button({
        id: 'img',
        caption: 'Insert Image',
        icon: 'fa fa-image',
        key: 'p',
        callback: '',
        default: 'Alt Text',
        content: '![%](insert_image_url)'
    });

    /*
    BLOCKQUOTE
     */
    mdEditor.add_button({
        id: 'quote',
        caption: 'Insert Blockquote',
        icon: 'fa fa-quote-left',
        key: 'q',
        callback: '',
        default: '',
        content: '> %'
    });

    /*
     BLOCKQUOTE
     */
    mdEditor.add_button({
        id: 'code',
        caption: 'Inline code',
        icon: 'fa fa-code',
        key: 'c',
        callback: '',
        default: 'code_goes_here',
        content: '`%`'
    });

    /*
     More Code
     */
    mdEditor.add_button({
        id: 'more',
        caption: 'More elements',
        icon: 'fa fa-toggle-down',
        key: 'm',
        callback: '',
        default: '',
        content: '',
        type: 'menu'
    });

    mdEditor.add_button({
        id: 'pre',
        owner: 'more',
        caption: 'Insert block code',
        icon: 'fa fa-code',
        key: '',
        callback: '',
        default: 'Code block goes here',
        content: '```\n%s\n```',
    });

    mdEditor.add_button({
        id: 'task',
        owner: 'more',
        caption: 'Insert task list',
        icon: 'fa fa-check',
        key: '',
        callback: '',
        default: 'Item list',
        content: '\n- [x] %\n- [x] Item list\n- [x] Item list\n\n',
    });

})(mdEditor);


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
        buttons: [],
        btn_tpl: '<button type="button" class="button" data-id="%id" data-editor="%e" accesskey="%k" title="%t">%c</button>',

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

            this.buttons[this.buttons.length] = {
                caption: data.caption,
                id: data.id,
                callback: data.callback,
                icon: data.icon,
                key: data.key,
                content: data.content
            };

        },

        render_buttons: function( id ){

            $toolbar = $("#" + id + "-buttons-container > .toolbar-1");

            for(i=0;i<this.buttons.length;i++){
                var btn = this.buttons[i];
                var tpl = this.btn_tpl;
                tpl = tpl.replace('%id', i);
                tpl = tpl.replace('%k', btn.key);
                tpl = tpl.replace('%e', id);
                if ( undefined != btn.icon ) {
                    tpl = tpl.replace('%t', btn.caption);
                    tpl = tpl.replace('%c', '<span class="' + btn.icon + '"></span>');
                } else {
                    tpl = tpl.replace('%c', btn.caption);
                }
                $toolbar.append(tpl);
            }

        },

        listeners: function(){

            var ed = this;

            $(".ed-container > .ed-buttons > .toolbar-1 > .button").click(function(){

                var id = $(this).data('id');
                if ( undefined == id || 0 > id )
                    return;

                if ( undefined == ed.buttons[id] )
                    return;

                var editor = ed.editors[$(this).data('editor')];

                if( 'function' == typeof ed.buttons[id].callback ){

                    ed.buttons[id].callback( editor );

                } else if ( 'string' == typeof ed.buttons[id].content ) {

                    var current = editor.getSelection();
                    var pos = editor.getCursor();
                    var content = ed.buttons[id].content;
                    if ( '' == current )
                        pos.ch += content.split('%')[0].length;

                    editor.replaceSelection( content.replace("%", current) );

                    if ('' == current )
                        editor.setCursor( pos );

                    editor.focus();

                }

            });

        }

    };

//})();

(function(mdEditor){

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
    })

})(mdEditor);


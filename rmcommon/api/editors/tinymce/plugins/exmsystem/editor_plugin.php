<?php
// $Id: editor_plugin.php 1015 2012-08-23 05:36:42Z i.bitcero $
/**
* Este archivo permite la inclusión de nuevos plugins desde otros módulos
*/
header('Content-type: text/javascript');
include_once '../../../../../../../mainfile.php';
require_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';
global $xoopsLogger;
$xoopsLogger->activated = false;

?>
/**
 * EXM System plugin
 */
(function() {
    var DOM = tinymce.DOM;
    
    tinymce.PluginManager.requireLangPack('exmsystem');
    
    tinymce.create('tinymce.plugins.EXMSystem', {
        
        <?php RMEvents::get()->run_event('rmcommon.tiny.plugin.controls'); ?>
        
        init : function(ed, url){
            var t = this;
            var tbId1 = ed.getParam('exmsystem_adv_toolbar1','toolbar2');
            var tbId2 = ed.getParam('exmsystem_adv_toolbar2','toolbar3');
            var tbId3 = ed.getParam('exmsystem_adv_toolbar3','toolbar4');
            var moreHTML = '<img src="' + url + '/img/trans.gif" class="mceEXMmore mceItemNoResize" title="'+ed.getLang('exmsystem.exm_more_alt')+'" />';
            var nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceEXMnextpage mceItemNoResize" title="'+ed.getLang('exmsystem.exm_page_alt')+'" />';
            
            /*if ( getUserSetting('hidetb', '0') == '1' )
                    ed.settings.exmsystem_adv_hidden = 0;*/
            
            // Hides the specified toolbar and resizes the iframe
            ed.onPostRender.add(function() {
                if ( ed.getParam('exmsystem_adv_hidden', 1) ) {
                    DOM.hide(ed.controlManager.get(tbId1).id);
                    if(ed.controlManager.get(tbId2)!=undefined)
                        DOM.hide(ed.controlManager.get(tbId2).id);
                    if(ed.controlManager.get(tbId3)!=undefined)
                        DOM.hide(ed.controlManager.get(tbId3).id);
                    
                    t._resizeIframe(ed, tbId1, 28);
                    t._resizeIframe(ed, tbId2, 28);
                    t._resizeIframe(ed, tbId3, 28);
                }
            });
            
            // Register commands
            ed.addCommand('EXM_More', function() {
                ed.execCommand('mceInsertContent', 0, moreHTML);
            });
            // Page
            ed.addCommand('EXM_Page', function() {
                ed.execCommand('mceInsertContent', 0, nextpageHTML);
            });
            // Advanced Toolbar
            ed.addCommand('EXM_Adv', function() {
                var cm = ed.controlManager;

                if (DOM.isHidden(ed.controlManager.get(tbId1).id)) {
                    cm.setActive('exm_adv', 1);
                    DOM.show(ed.controlManager.get(tbId1).id);
                    if(ed.controlManager.get(tbId2)!=undefined)
                        DOM.show(ed.controlManager.get(tbId2).id);
                    if(ed.controlManager.get(tbId3)!=undefined);
                        DOM.show(ed.controlManager.get(tbId3).id);
                    t._resizeIframe(ed, tbId, -28);
                    ed.settings.exmsystem_adv_hidden = 0;
                } else {
                    cm.setActive('exm_adv', 0);
                    DOM.hide(ed.controlManager.get(tbId1).id);
                    if(ed.controlManager.get(tbId2)!=undefined)
                        DOM.hide(ed.controlManager.get(tbId2).id);
                    if(ed.controlManager.get(tbId3)!=undefined);
                        DOM.hide(ed.controlManager.get(tbId3).id);
                    
                    t._resizeIframe(ed, tbId1, 28);
                    t._resizeIframe(ed, tbId2, 28);
                    t._resizeIframe(ed, tbId3, 28);
                    ed.settings.exmsystem_adv_hidden = 1;
                }
            });
            
            // EXM Image Manager
            ed.addCommand('mceEXMImg', function() {
                ed.windowManager.open({
                    file : '<?php echo RMCURL; ?>/include/tiny-images.php',
                    width : 600 + parseInt(ed.getLang('exmsystem.delta_width', 0)),
                    height : 600 + parseInt(ed.getLang('exmsystem.delta_height', 0)),
                    inline : 1,
                    title: '<?php _e('Insert Image','rmcommon'); ?>',
                    maximizable: 'true'
                }, {
                    plugin_url : url
                });
            });
            
            // EXM Icons plugin
            // Register commands
            ed.addCommand('mceExmIcons', function() {
                ed.windowManager.open({
                    file : '<?php echo RMCURL; ?>/include/emotions.php',
                    width : 300 + parseInt(ed.getLang('exmsystem.delta_width', 0)),
                    height : 250 + parseInt(ed.getLang('exmsystem.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            
            // Register buttons
            ed.addButton('exm_more', {
                title : 'exmsystem.exm_more_desc',
                image : url + '/img/more.gif',
                cmd : 'EXM_More'
            });

            ed.addButton('exm_page', {
                title : 'exmsystem.exm_page_desc',
                image : url + '/img/page.gif',
                cmd : 'EXM_Page'
            });

            ed.addButton('exm_adv', {
                title : 'exmsystem.exm_adv_desc',
                image : url + '/img/toolbars.gif',
                cmd : 'EXM_Adv'
            });
            
            // Image Manager
            ed.addButton('exm_img', {
                title : 'exmsystem.exm_img_desc', 
                cmd : 'mceEXMImg', 
                image : url+'/img/exmimg.png'
            });
            
            // Icons plugin
            // Register buttons
            ed.addButton('exm_icons', {
                title : 'exmsystem.exm_icons_desc', 
                cmd : 'mceExmIcons', 
                image : url+'/img/icon.png'
            });
            
            // Add class "alignleft", "alignright" and "aligncenter" when selecting align for images.
            ed.addCommand('JustifyLeft', function() {
                var n = ed.selection.getNode();

                if ( n.nodeName != 'IMG' )
                    ed.editorCommands.mceJustify('JustifyLeft', 'left');
                else ed.plugins.exmsystem.do_align(n, 'alignleft');
            });

            ed.addCommand('JustifyRight', function() {
                var n = ed.selection.getNode();

                if ( n.nodeName != 'IMG' )
                    ed.editorCommands.mceJustify('JustifyRight', 'right');
                else ed.plugins.exmsystem.do_align(n, 'alignright');
            });

            ed.addCommand('JustifyCenter', function() {
                var n = ed.selection.getNode(), P = ed.dom.getParent(n, 'p'), DL = ed.dom.getParent(n, 'dl');

                if ( n.nodeName == 'IMG' && ( P || DL ) )
                    ed.plugins.exmsystem.do_align(n, 'aligncenter');
                else ed.editorCommands.mceJustify('JustifyCenter', 'center');
            });
            
            // Add listeners to handle more break
            t._handleMoreBreak(ed, url);
            
            <?php
                // Incluimos los plugins y datos de otros módulos
                // para crear botones y comandos en TinyMCE
                // Los métodos registrados deben retornar código javascript
                // funcional en el método init de tinymce
                RMEvents::get()->run_event('rmcommon.tinymce.plugin.loading');
            ?>
        
        },
        
    
        getInfo : function() {
            return {
                longname : 'EXMSystem Plugin',
                author : 'BitC3R0', // add Moxiecode?
                authorurl : 'http://redmexico.com.mx',
                infourl : 'http://redmexico.com.mx',
                version : '3.0'
            };
        },
        
        // Internal functions
        do_align : function(n, a) {
            var P, DL, DIV, cls, c, ed = tinyMCE.activeEditor;

            if ( /^(mceItemFlash|mceItemShockWave|mceItemWindowsMedia|mceItemQuickTime|mceItemRealMedia)$/.test(n.className) )
                return;

            P = ed.dom.getParent(n, 'p');
            DL = ed.dom.getParent(n, 'dl');
            DIV = ed.dom.getParent(n, 'div');

            if ( DL && DIV ) {
                cls = ed.dom.hasClass(DL, a) ? 'alignnone' : a;
                DL.className = DL.className.replace(/align[^ '"]+\s?/g, '');
                ed.dom.addClass(DL, cls);
                c = (cls == 'aligncenter') ? ed.dom.addClass(DIV, 'mceIEcenter') : ed.dom.removeClass(DIV, 'mceIEcenter');
            } else if ( P ) {
                cls = ed.dom.hasClass(n, a) ? 'alignnone' : a;
                n.className = n.className.replace(/align[^ '"]+\s?/g, '');
                ed.dom.addClass(n, cls);
                if ( cls == 'aligncenter' )
                    ed.dom.setStyle(P, 'textAlign', 'center');
                else if (P.style && P.style.textAlign == 'center')
                    ed.dom.setStyle(P, 'textAlign', '');
            }

            ed.execCommand('mceRepaint');
        },
        
        // Resizes the iframe by a relative height value
        _resizeIframe : function(ed, tb_id, dy) {
            var ifr = ed.getContentAreaContainer().firstChild;

            DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
            ed.theme.deltaHeight += dy; // For resize cookie
        },
        
        _handleMoreBreak : function(ed, url) {
            var moreHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mceEXMmore mceItemNoResize" title="'+ed.getLang('exmsystem.exm_more_alt')+'" />';
            var nextpageHTML = '<img src="' + url + '/img/trans.gif" class="mceEXMnextpage mceItemNoResize" title="'+ed.getLang('exmsystem.exm_page_alt')+'" />';

            // Load plugin specific CSS into editor
            ed.onInit.add(function() {
                ed.dom.loadCSS(url + '/css/content.css');
            });

            // Display morebreak instead if img in element path
            ed.onPostRender.add(function() {
                if (ed.theme.onResolveName) {
                    ed.theme.onResolveName.add(function(th, o) {
                        if (o.node.nodeName == 'IMG') {
                            if ( ed.dom.hasClass(o.node, 'mceEXMmore') )
                                o.name = 'exmmore';
                            if ( ed.dom.hasClass(o.node, 'mceEXMnextpage') )
                                o.name = 'exmpage';
                        }

                    });
                }
            });

            // Replace morebreak with images
            ed.onBeforeSetContent.add(function(ed, o) {
                o.content = o.content.replace(/<!--more(.*?)-->/g, moreHTML);
                o.content = o.content.replace(/<!--nextpage-->/g, nextpageHTML);
            });

            // Replace images with morebreak
            ed.onPostProcess.add(function(ed, o) {
                if (o.get)
                    o.content = o.content.replace(/<img[^>]+>/g, function(im) {
                        if (im.indexOf('class="mceEXMmore') !== -1) {
                            var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
                            im = '<!--more'+moretext+'-->';
                        }
                        if (im.indexOf('class="mceEXMnextpage') !== -1)
                            im = '<!--nextpage-->';

                        return im;
                    });
            });

            // Set active buttons if user selected pagebreak or more break
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('exm_page', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceEXMnextpage'));
                cm.setActive('exm_more', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceEXMmore'));
            });
        }
    
    });
    
    <?php
        // Evento para registrar nuevas funciones en el plugin exmsystem
        RMEvents::get()->run_event('rmcommon.tinymce.plugin.functions','');
    ?>
    
    // Register plugin
    tinymce.PluginManager.add('exmsystem', tinymce.plugins.EXMSystem);
    
})();
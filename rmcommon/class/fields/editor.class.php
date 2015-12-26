<?php
// $Id: editor.class.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase controladora para campos de edicin de contenido
 */
class RMFormEditor extends RMFormElement
{
	private $_width = '';
	private $_height = '';
	private $_default = '';
	private $_type = 'tiny';
	private $_theme = '';
	private $_action = '';
	/**
	* Indica si se muestra o no el combo para cambiar tipo de editor
	*/
	private $_change = 0;
	/**
	* Especifica el parámetro que sirve como base para el cambio
	* de tipo de edito
	*/
	private $_eles = array();
	/**
	 * Variables utilizadas con el editor tiny
	 */
	private $_tinycss = '';
    /**
    * Almacena los botones (orden) que se utilizaran en el editor exmCode
    */
    private $ex_plugins = 'dropdown,texts,fonts,email,link,xcode,xquote,rmimage,emotions,more,chars';
    private $ex_buttons = 'bold,italic,underline,strikeout,separator_t,left,center,right,separator_t,fontsize,font,fontcolor,separator_t,more,bottom,top,separator_b,link,email,xcode,xquote,separator_b,images,emotions,separator_b,chars,page';

    /**
     * Markdown editor options
     */
    private $md_options = array();

	/**
	 * @param string $caption Texto del campo
	 * @param string $name Nombre de este campo
	 * @param string $width Ancho del campo. Puede ser el valor en formato pixels (300px) o en porcentaje (100%)
	 * @param string $height Alto de campo. El valor debe ser pasado en formato pixels (300px).
	 * @param string $default Texto incial al cargar el campo. POr defecto se muestra vaco.
	 * @param string $type Tipo de Editor. Posibles valores: tiny, html, xoops, simple, markdown
	 */
	function __construct($caption, $name = null, $width='100%', $height='300px', $default='', $type='', $change=1, $ele=array('op')){
        
        $rmc_config = RMSettings::cu_settings();
        
		$tcleaner = TextCleaner::getInstance();

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, '');
            $this->setWithDefaults('id', $tcleaner->sweetstring($name), '');
            $this->setWithDefaults('width', $width, '100%');
            $this->setWithDefaults('height', $height, '300px');
            $this->setWithDefaults('value', isset($_REQUEST[$name]) ? $tcleaner->stripslashes($_REQUEST[$name]) : $tcleaner->stripslashes($default), '');
            $this->setWithDefaults('type', $type, $rmc_config->editor_type);
            $this->setWithDefaults('change', $change, 1);
            $this->setWithDefaults('elements', $ele, array('op'));
        }

        $this->setIfNotSet('type', $type=='' ? $rmc_config->editor_type : $type);
        $this->setIfNotSet('value', '');
        
	}
	/**
	 * Establece el tipo de editor a utilizar
	 */
	public function getType(){
		return $this->get('type');
	}
	/**
	 * Establece el tema a utilizar en tiny
     * @param string $theme
	 */
	public function setTheme($theme){
		$this->set('theme', $theme);
	}
	/**
	 * Cambia el valor action del formulario en el cambio de tipo de editor
     *
     * @param string $action
	 */
	public function editorFormAction($action){
		$this->set('action', $action);
	}
	/**
	 * Generamos el codigo HTML para el editor seleccionado
	 * @return string
	 */
	public function render(){
		/**
		* Agregamos la opción para cambiar el tipo de editor
		*/
		$ret = '';

        if('tiny' != $this->get('type')){
            RMTemplate::getInstance()->add_style('editors.min.css', 'rmcommon', ['id' => 'editors-css']);
            RMTemplate::getInstance()->add_script('cu-handler.js', 'rmcommon', ['id' => 'cuhandler']);
        }
        
		switch ($this->get('type')){
			case 'simple':
				$ret .= $this->renderTArea();
				break;
			/*case 'xoops':
				$ret .= $this->renderExmCode();
				break;*/
			case 'html':
				$ret .= $this->renderHTML();
				break;
            case 'markdown':
                $ret .= $this->renderMarkdown();
                break;
            case 'tiny':
            default:
                $ret .= $this->renderTiny();
                break;
		}
		
		return $ret;
	}
	
	public function renderTArea(){
        global $cuIcons;

        RMTemplate::get()->add_style('simple-editor.min.css', 'rmcommon');
		$rtn = "<div class=\"ed-container\">";
        $plugins = array();
        $plugins = RMEvents::get()->trigger('rmcommon.editor.top.plugins', $plugins, 'simple', $this->id());
        $plugins = RMEvents::get()->trigger('rmcommon.simple.editor.plugins', $plugins, $this->id());
        $this->renderAttributeString();
        if (!empty($plugins)){
            $rtn .= '<div class="ed-plugins"><span class="plugin">';
            $rtn .= implode('</span><span class="plugin">', $plugins);
            $rtn .= "</span>";
            $rtn .= '<button type="button" class="plugin full-screen" accesskey="s" title="'. __('Toggle full screen [S]', 'rmcommon') . '">'.$cuIcons->getIcon('svg-rmcommon-fullscreen').$cuIcons->getIcon('svg-rmcommon-exit-fullscreen').'</button></div>';
        }
        $rtn .= "<div class=\"txtarea-container\" style='height: ".$this->get('height').";'><textarea class='xc-editor' id='".$this->get('id')."' name='".$this->get('name')."'>".$this->get('value')."</textarea></div>
                 </div>";
        return $rtn;
	}
	/**
	 * Set de funciones útiles nicamente con el editor TinyMCE
     * 
     * @param string $url
     * @param RMForm $form
	 */
	public function tinyCSS($url, RMForm &$form){
        $form->tinyCSS($url);
	}
    
	/**
	 * Genera el cdigo HTML para el editor TINY
	 * @return string
	 */
	private function renderTiny(){
		global $rmc_config, $xoopsUser;

        $this->renderAttributeString();
        
		TinyEditor::getInstance()->add_config('elements',$this->get('id'), true);
		RMTemplate::get()->add_style('editor-tiny.min.css','rmcommon');
		RMTemplate::get()->add_script( 'editor.js','rmcommon');
		RMTemplate::get()->add_script( 'quicktags.min.js','rmcommon' );
        RMTemplate::get()->add_script(RMCURL.'/api/editors/tinymce/tiny_mce.js');
		RMTemplate::get()->add_inline_script(TinyEditor::getInstance()->get_js());
		RMTemplate::get()->add_inline_script('edToolbar("' . $this->get('id') . '");', 1);

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'tiny', $this->get('id'));

		$rtn = '
		<div class="ed-container" id="ed-cont-'.$this->get('id').'" style="width: 100%;">
        <div class="es-editor" style="width: 100%;">
        <div class="es-plugins">
            <span class="plugin">'.implode('</span><span class="plugin">', $plugins).'</span>
        </div>
        <a class="edButtonHTML'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'html' ? ' active' : '').'" onclick="switchEditors.go(\''.$this->get('id').'\', \'html\'); return false;"><span class="fa fa-code"></span> HTML</a>
        <a class="edButtonPreview'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'tinymce' ? ' active' : '').'" onclick="switchEditors.go(\''.$this->get('id').'\', \'tinymce\'); return false;"><span class="fa fa-eye"></span> Visual</a>
        </div>
        <div'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'html' ? ' class="showing"' : '').'>
        <div class="quicktags"><div id="ed_toolbar_' . $this->get('id') . '"></div></div>
        <textarea onchange="tinyMCE.activeEditor.save();" id="'.$this->get('id').'" name="'.$this->get('name').'" style="width: 100%; height: '.$this->get('height').';" class="'.$this->getClass().'">'.$this->get('default').'</textarea></div>
        </div>';
		return $rtn;
	}
	
	/**
	* HTML Editor
	* @since 1.5
	*/
	private function renderHTML(){
        global $cuIcons;

        $this->renderAttributeString();
        
		RMTemplate::get()->add_script("quicktags.min.js", 'rmcommon', array('footer' => 1));
		RMTemplate::get()->add_style('html-editor.min.css','rmcommon');
		RMTemplate::getInstance()->add_fontawesome();
		RMTemplate::get()->add_inline_script('edToolbar("' . $this->get('id') . '");', 1);
		$rtn = "\n<div class='ed-container html_editor_container' style='width: " . $this->get('width') . ";' id='".$this->get('id')."-ed-container'>";
        $plugins = array();

        // Get external plugins
        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'html', $this->get('id'));

        if ( !empty( $plugins ) ){
            $rtn .= '<div class="ed-plugins">
                        <span class="plugin">'.implode('</span><span class="plugin">', $plugins).'</span>
                        <button type="button" class="plugin full-screen" accesskey="s" title="'. __('Toggle full screen [S]', 'rmcommon') . '">'.$cuIcons->getIcon('svg-rmcommon-fullscreen').$cuIcons->getIcon('svg-rmcommon-exit-fullscreen').'</button>
                     </div>';
        }

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.html.editor.plugins', $plugins, $this->get('id'));
        
		$rtn .= "<div class=\"quicktags\"><div id=\"ed_toolbar_" . $this->get('id') . "\"></div></div>".(!empty
		($plugins) ? "<div class='ed-qt-plugins'><span class='plugin'>".implode('</span><span class="plugin">', $plugins)."</span></div>" : '')."
		<div class='txtarea-container' style='height: ".$this->get('height').";'><textarea id='".$this->get('id')."' name='".$this->get('name')."' class='".$this->get('class')."'>".$this->get('default')."</textarea></div>
		</div>";
		return $rtn;
	}

    /**
     * MARKDOWN EDITOR
     */
    public function set_markdown_options( $options ){
        $this->md_options = $options;
    }

    public function get_markdown_options(){
        return $this->md_options;
    }

    public function renderMarkdown(){

        $editor = new Editor_Markdown( $this->get('id'), $this->md_options );
        $editor->attr( 'style', 'height: ' . $this->get('height') );
        $editor->content = $this->get('value');
        return $editor->render();

    }
    
    /**
    * Establece los botones a mostrar en el editor
    * 
    * @param string $buttons
    */
    public function exmcode_buttons($buttons){
        if ($buttons=='') return;
        
        $this->ex_buttons = $buttons;
    }
}

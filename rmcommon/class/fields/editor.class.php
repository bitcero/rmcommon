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
	 * @param string $caption Texto del campo
	 * @param string $name Nombre de este campo
	 * @param string $width Ancho del campo. Puede ser el valor en formato pixels (300px) o en porcentaje (100%)
	 * @param string $height Alto de campo. El valor debe ser pasado en formato pixels (300px).
	 * @param string $default Texto incial al cargar el campo. POr defecto se muestra vaco.
	 * @param string $type Tipo de Editor. Posibles valores: FCKeditor, DHTML
	 */
	function __construct($caption, $name, $width='100%', $height='300px', $default='', $type='', $change=1, $ele=array('op')){
        
        $rmc_config = RMSettings::cu_settings();
        
		$tcleaner = TextCleaner::getInstance();
		$this->setCaption($caption);
		$this->setName($name);
		$this->_width = $width;
		$this->_height = $height;
		$this->_default = isset($_REQUEST[$name]) ? $tcleaner->stripslashes($_REQUEST[$name]) : $tcleaner->stripslashes($default);
		$this->_type = $type=='' ? $rmc_config->editor_type : $type;
        $this->_type = strtolower($this->_type);
        $this->_change = $change;
        $this->_eles = $ele;
        
	}
	/**
	 * Establece el tipo de editor a utilizar
	 */
	public function setType($value){
		$this->_type = $value;
	}
	public function getType(){
		return $this->_type;
	}
	/**
	 * Establece el tema a utilizar en tiny
	 */
	public function setTheme($theme){
		$this->_theme = $theme;
	}
	/**
	* @desc Cambia el valor action del formulario en el cambio de tipo de editor
	*/
	public function editorFormAction($action){
		$this->_action = $action;
	}
	/**
	 * Generamos el cdigo HTML para el editor seleccionado
	 * @return string
	 */
	public function render(){
		/**
		* Agregamos la opción para cambiar el tipo de editor
		*/
		$ret = '';
        
		switch ($this->_type){
			case 'simple':
				$ret .= $this->renderTArea();
				break;
			case 'xoops':
				$ret .= $this->renderExmCode();
				break;
			case 'html':
				$ret .= $this->renderHTML();
				break;
            case 'tiny':
            default:
                $ret .= $this->renderTiny();
                break;
		}
		
		return $ret;
	}
	
	public function renderTArea(){
        RMTemplate::get()->add_style('editor-simple.css', 'rmcommon');
		$rtn = "<div class=\"ed-container\" style=\"width: $this->_width\">";
        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'simple', $this->id());
        $plugins = RMEvents::get()->run_event('rmcommon.simple.editor.plugins', $plugins, $this->id());
        if (!empty($plugins)){
            $rtn .= '<div class="ed-plugins"><span class="plugin">';
            $rtn .= implode('</span><span class="plugin">', $plugins);
            $rtn .= "</span></div>";
        }
        $rtn .= "<textarea class='xc-editor' id='".$this->getName()."' name='".$this->getName()."' style='height: ".$this->_height.";'>".$this->_default."</textarea>
                 </div>";
        return $rtn;
	}
	/**
	 * Set de funciones útiles nicamente con el editor TinyMCE
	 */
	public function tinyCSS($url, EXMForm &$form){
        $form->tinyCSS($url);
	}
    
	/**
	 * Genera el cdigo HTML para el editor TINY
	 * @return string
	 */
	private function renderTiny(){
		global $rmc_config, $xoopsUser;
		TinyEditor::getInstance()->add_config('elements',$this->id(), true);
		RMTemplate::get()->add_style('editor-tiny.css','rmcommon');
		RMTemplate::get()->add_script( 'editor.js','rmcommon', array('directory' => 'include') );
		RMTemplate::get()->add_script( 'quicktags.js','rmcommon' );
        RMTemplate::get()->add_script(RMCURL.'/api/editors/tinymce/tiny_mce.js');
		RMTemplate::get()->add_head_script(TinyEditor::getInstance()->get_js());

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'tiny', $this->id());

		$rtn = '
		<div class="ed-container" id="ed-cont-'.$this->id().'" style="width: 100%;">
        <div class="es-editor" style="width: 100%;">
        <div class="es-plugins">
            <span class="plugin">'.implode('</span><span class="plugin">', $plugins).'</span>
        </div>
        <a class="edButtonHTML'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'html' ? ' active' : '').'" onclick="switchEditors.go(\''.$this->id().'\', \'html\'); return false;"><span class="fa fa-code"></span> HTML</a>
        <a class="edButtonPreview'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'tinymce' ? ' active' : '').'" onclick="switchEditors.go(\''.$this->id().'\', \'tinymce\'); return false;"><span class="fa fa-eye"></span> Visual</a>
        </div>
        <div'.(isset($_COOKIE['editor']) && $_COOKIE['editor'] == 'html' ? ' class="showing"' : '').'>
        <div class="quicktags"><script type="text/javascript">edToolbar(\''.$this->id().'\')</script></div>
        <textarea onchange="tinyMCE.activeEditor.save();" id="'.$this->id().'" name="'.$this->getName().'" style="width: 100%; height: '.$this->_height.';" class="'.$this->getClass().'">'.$this->_default.'</textarea></div>
        </div>';
		return $rtn;
	}
	
	/**
	* HTML Editor
	* @since 1.5
	*/
	private function renderHTML(){
		RMTemplate::get()->add_script("quicktags.js", 'rmcommon');
		RMTemplate::get()->add_style('editor-html.css','rmcommon');
		$rtn = "\n<div class='ed-container html_editor_container' style='width: $this->_width;' id='".$this->id()."-ed-container'>";
        $plugins = array();

        // Get external plugins
        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'html', $this->id());

        if ( !empty( $plugins ) )
            $rtn .= '<div class="ed-plugins"><span class="plugin">'.implode('</span><span class="plugin">', $plugins).'</span></div>';

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.html.editor.plugins', $plugins, $this->id());
        
		$rtn .= "<div class=\"quicktags\">
		<script type=\"text/javascript\">edToolbar('".$this->id()."')</script>".(!empty($plugins) ? "<div class='ed-qt-plugins'><span class='plugin'>".implode('</span><span class="plugin">', $plugins)."</span></div>" : '')."</div>
		<div class='txtarea_container'><textarea id='".$this->id()."' name='".$this->getName()."' style='height: ".$this->_height.";' class='".$this->getClass()."'>".$this->_default."</textarea></div>
		</div>";
		return $rtn;
	}
	
	private function renderExmCode(){
		RMTemplate::get()->add_script(RMCURL."/api/editors/exmcode/editor-exmcode.php?id=".$this->id());
		RMTemplate::get()->add_script(RMCURL."/include/js/colorpicker.js");
		RMTemplate::get()->add_style('editor-exmcode.css','rmcommon');
		RMTemplate::get()->add_style('colorpicker.css','rmcommon');

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'exmcode', $this->id());

		$rtn = 	"<div class='ed-container' id='".$this->id()."-ed-container' width='$this->_width'>";
		$rtn .= "<div class='ed-plugins' id='".$this->id()."-ed-plugins'><span class='plugin'>".implode('</span> <span class="plugin">', $plugins).'</span></div>';
		$rtn .= "<div class='ed_buttons' id='".$this->id()."-ec-container'>";
        $rtn .= "<div class='row_top'></div><div class='row_bottom'></div>";
		$rtn .= "</div>";
		$rtn .= "<textarea id='".$this->id()."' name='".$this->getName()."' style='height: ".$this->_height.";' class='".$this->getClass()."'>".$this->_default."</textarea>";
		$rtn .= "</div>";
        // buttons
        $tplugins = RMEvents::get()->run_event('rmcommon.exmcode.plugins', $this->ex_plugins);
        $tplugins = explode(',',$tplugins);
        $plugins = '';
        foreach ($tplugins as $p){
            $plugins .= $plugins=='' ? $p.': true' : ','.$p.': true';
        }
        RMTemplate::get()->add_head("<script type=\"text/javascript\">\nvar ".$this->id()."_buttons = \"".RMEvents::get()->run_event('rmcommon.exmcode.buttons', $this->ex_buttons)."\";\nvar ".$this->id()."_plugins = {".$plugins."};\n</script>");
		return $rtn;
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

<?php
// $Id: theme.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormTheme extends RMFormElement
{
	private $multi = 0;
	private $type = 0;
	private $selected = array();
	private $cols = 2;
	private $section = '';
	
	/**
	 * Constructor
	 * @param string $caption
	 * @param string $name Nombre del campo
	 * @param int $multi Selecciona multiple activada o desactivada
	 * @param int $type 0 = Select, 1 = Tabla
	 * @param int $selected Valor seleccionado por defecto
	 * @param array $selected Grupo de vlores seleccionado por defecto
	 * @param int $cols Numero de columnas para la tabla o filas para un campo select multi
	 * @param string 'GUI' for admin theme
	 */
	function __construct($caption, $name, $multi = 0, $type = 0, $selected = null, $cols = 2, $section=''){
		$this->setName($name);
		$this->setCaption($caption);
		$this->multi = $multi;
		$this->type = $type;
		$this->cols = $cols;
		$this->selected = $selected;
		$this->section = $section;
	}
	function multi(){
		return $this->multi;
	}
	function setMulti($value){
		return $this->multi = $value;
	}
	function type(){
		return $this->type;
	}
	function setType($value){
		return $this->type = $value;
	}
	function selected(){
		return $this->selected;
	}
	function setSelected($value){
		return $this->selected = $value;
	}
	
	function render(){
        if($this->section=='GUI')
		    $dirs = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH.'/modules/rmcommon/themes', '');
        else
            $dirs = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH.'/themes', '');
            
        $themes = array();
        foreach($dirs as $dir => $v){
            
            if($this->section=='GUI'){
                if(!file_exists(XOOPS_ROOT_PATH.'/modules/rmcommon/themes/'.$dir.'/admin-gui.php')) continue;
            }else{
                if(!file_exists(XOOPS_ROOT_PATH.'/themes/'.$dir.'/theme.html')) continue;
            }

            // Read theme name
            $file = file_get_contents( XOOPS_ROOT_PATH.'/modules/rmcommon/themes/'.$dir.'/admin-gui.php' );
            preg_match("/Theme name:\s{0,}(.*)?\r/m", $file, $name );
            $themes[$dir] = isset( $name[1] ) ? $name[1] : __('Unknow', 'rmcommon');

        }
        unset( $name );
    
		if ($this->type){
			$rtn = '<ul class="rmoptions_container">';
			foreach ($themes as $k => $name){
				if ($this->multi){
					$rtn .= "<li><label><input type='checkbox' value='$k' name='".$this->getName()."[]' id='".$this->id()."'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " checked='checked'" : '') : '')." /> $name</label></li>";
				} else {
					$rtn .= "<li><label><input type='radio' value='$k' name='".$this->getName()."' id='".$this->id()."'".(!empty($this->selected) ? ($k == $this->selected ? " checked='checked'" : '') : '')." /> $name</label></li>";
				}
			}
			$rtn .= "</ul>";
		} else {
			if ($this->multi){
				$rtn = "<select name='".$this->getName()."[]' id='".$this->id()."' size='6' multiple='multiple' class=\"form-control ".$this->getClass()."\">";
				foreach ($themes as $k => $name){
					$rtn .= "<option value='$k'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " selected='selected'" : '') : '').">$name</option>";
				}
				$rtn .= "</select>";
			} else {
				$rtn = "<select name='".$this->getName()."' id='".$this->id()."' class=\"form-control ".$this->getClass()."\">";
				foreach ($themes as $k => $name){
					$rtn .= "<option value='$k'".(!empty($this->selected) ? ($k==$this->selected ? " selected='selected'" : '') : '').">$name</option>";
				}
				$rtn .= "</select>";
			}
		}
		
		return $rtn;
		
	}
}

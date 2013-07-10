<?php
// $Id: language.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormLanguageField extends RMFormElement
{
	private $multi = 0;
	private $type = 0;
	private $selected = array();
	private $cols = 2;
	
	/**
	 * Constructor
	 * @param string $caption
	 * @param string $name Nombre del campo
	 * @param int $multi Selecciona multiple activada o desactivada
	 * @param int $type 0 = Select, 1 = Tabla
	 * @param $selected Valor seleccionado por defecto
	 * @param array $selected Grupo de vlores seleccionado por defecto
	 * @param int $cols Numero de columnas para la tabla o filas para un campo select multi
	 */
	function __construct($caption, $name, $multi = 0, $type = 0, $selected = null, $cols = 2){
		$this->setName($name);
		$this->setCaption($caption);
		$this->multi = $multi;
		$this->type = $type;
		$this->cols = $cols;
		$this->selected = $selected;
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
		$files = XoopsLists::getFileListAsArray(XOOPS_ROOT_PATH.'/modules/rmcommon/lang', '');
        $langs = array();
        $langs['en_US'] = 'en';
        foreach($files as $file => $v){
            
            if(substr($file, -3)!='.mo') continue;
            
            $langs[substr($file, 0, -3)] = substr($file, 0, -3);
            
        }
		if ($this->type){
			$rtn = '<ul class="rmoptions_container">';
			$i = 1;
			foreach ($langs as $k){
				if ($this->multi){
					$rtn .= "<li><label><input type='checkbox' value='$k' name='".$this->getName()."[]' id='".$this->id()."[]'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " checked='checked'" : '') : '')." /> $k</label></li>";
				} else {
					$rtn .= "<li><label><input type='radio' value='$k' name='".$this->getName()."' id='".$this->id()."'".(!empty($this->selected) ? ($k == $this->selected ? " checked='checked'" : '') : '')." /> $k</label></li>";
				}
				$i++;
			}
			$rtn .= "</ul>";
		} else {
			if ($this->multi){
				$rtn = "<select name='".$this->getName()."[]' id='".$this->id()."[]' size='$this->cols' multiple='multiple'>";
				foreach ($langs as $k){
					$rtn .= "<option value='$k'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " selected='selected'" : '') : '').">$k</option>";
				}
				$rtn .= "</select>";
			} else {
				$rtn = "<select name='".$this->getName()."' id='".$this->id()."'>";
				foreach ($langs as $k){
					$rtn .= "<option value='$k'".(!empty($this->selected) ? ($k==$this->selected ? " selected='selected'" : '') : '').">$k</option>";
				}
				$rtn .= "</select>";
			}
		}
		
		return $rtn;
		
	}
}

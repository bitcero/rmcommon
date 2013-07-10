<?php
// $Id: checks.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para la creacin y manejo de campos CHECKBOX
 */
class RMFormCheck extends RMFormElement
{
	private $asTable = false;
	private $tableCols = 3;
	private $_options = array();
	/**
	 * @param string $caption Texto de la etiqueta
	 */
	function __construct($caption){
		$this->setCaption($caption);
	}
	/**
	 * Agrega una nueva casilla (checkbox) al elemento.
	 * @param string $caption Texto de la casilla
	 * @param string $name Nombre de la casilla
	 * @param mixed $value Valor de la casilla
	 * @param int $state Activada, descativada (1, 0)
	 */
	public function addOption($caption, $name, $value, $state=0){
		$rtn = array();
		$rtn['caption'] = $caption;
		$rtn['value'] = $value;
		$rtn['state'] = $state;
		$rtn['name'] = $name;
		$this->_options[] = $rtn;
	}
	/** 
	 * Devuelve un array con las casilla del elemento.
	 * @return array
	 */
	public function getOptions(){
		return $this->_options;
	}
	/**
	 * Genera el cdigo HTML necesario para mostrar el campo.
	 * @return string
	 */
	function render(){
		$rtn = '';
		if ($this->asTable){
			$rtn .= "<table width='100%' cellspacing='1' cellpadding='2' border='0'><tr>";
			$cols = 1;
			foreach ($this->_options as $k => $v){
				if ($cols>$this->tableCols){
					$rtn .= '</tr><tr>';
					$cols = 1;
				}
				$rtn .= "<td><label><input type='checkbox' name='$v[name]' id='".$this->id()."' value='$v[value]' ";
				//if ($v['state']==1){
				$rtn .= isset($_REQUEST[$v['name']]) && $_REQUEST[$v['name']]==$v['value'] ? "checked='checked' " : ($v['state']==1 ? "checked='checked' " : '');
				//}
				$rtn .= "/> $v[caption]</label></td>";
				$cols++;
			}
			$rtn .= "</tr></table>";
		} else {
			foreach ($this->_options as $k => $v){
				$rtn .= "<input type='checkbox' name='$v[name]' id='".$this->id()."' value='$v[value]' ";
				//if ($v['state']==1){
				//	$rtn .= "checked='checked' ";
				//}
				$rtn .= isset($_REQUEST[$v['name']]) && $_REQUEST[$v['name']]==$v['value'] ? "checked='checked' " : ($v['state']==1 ? "checked='checked' " : '');
				$rtn .= "/> $v[caption]<br />";
			}
		}
		return $rtn;
	}
	/**
	 * Formatea la lista de checkboxes mediante una table
	 */
	function asTable($cols=3){
		$this->asTable = true;
		if ($cols<=0) $cols = 3;
		$this->tableCols = $cols;
	}
}

/**
 * Clase para la creacin y manejo de campos RADIO
 */
class RMFormRadio extends RMFormElement
{

	private $_options = array();
	private $_break;
    private $_type = 0;
    private $_cols = 3;
    
	/**
	 * @param string $caption Texto de la etiqueta.
	 * @param string $name Nombre del campo.
	 * @param string $salto Separador de elementos (&nbsp;, <br />, u otro caracter HTML)
     * @param int $type 1 = Tabla, 0 = Lista
     * @param int $cols Numero de columnas de la tabla
	 */
	public function __construct($caption, $name, $salto=0, $type = 0, $cols = 3){
		$this->setCaption($caption);
		$this->setName($name);
        $this->_type = $type;
        $this->_cols = $cols;
		if ($salto==0){ $this->_break = '<br />'; } else { $this->_break = '&nbsp;&nbsp;'; }
	}
	/**
	 * Agrega una nueva opcion (radio) al elemento
	 * @param string $caption Texto de la etiqueta
	 * @param mixed $value valor del elemento
	 * @param int $state 0 Desactivado, 1 Activado
	 */
	public function addOption($caption, $value, $state = 0, $extra = ''){
		$rtn = array();
		$rtn['caption'] = $caption;
		$rtn['value'] = $value;
		$rtn['state'] = $state;
        $rtn['extra'] = $extra;
		$this->_options[] = $rtn;
	}
	/**
	 * Devuelve el array con las opciones (radios) del elemento.
	 * @return array
	 */
	public function getOptions(){
		return $this->_options;
	}
	/**
	 * Genera el cdigo HTML para mostrar el elemento
	 * @return string
	 */
	public function render(){
		$rtn = '';
        
        if ($this->_type){
            
            $rtn .= "<table cellspacing='1' cellpadding='2' border='0'><tr>";
            $i = 1;
            foreach ($this->_options as $k => $v){
                if ($i>$this->_cols){
                    $i = 1;
                    $rtn .= "</tr><tr>";
                }
                $rtn .= "<td><label><input name='".$this->getName()."' id='".$this->id()."' type='radio' value='$v[value]' ";
                $rtn .= isset($_REQUEST[$this->getName()]) && $_REQUEST[$this->getName()]==$v['value'] ? "checked='checked' " : ($v['state']==1 ? "checked='checked' " : '');
                $rtn .= ($v['extra']!='' ? "$v[extra] " : '')."/> $v[caption]</label></td>";
                $i++;
            }
            $rtn .= "</tr></table>";
            
        } else {
        
		    foreach ($this->_options as $k => $v){
			    $rtn .= "<label><input name='".$this->getName()."' id='".$this->id()."' type='radio' value='$v[value]' ";
			    $rtn .= isset($_REQUEST[$this->getName()]) && $_REQUEST[$this->getName()]==$v['value'] ? "checked='checked' " : ($v['state']==1 ? "checked='checked' " : '');
			    $rtn .= ($v['extra']!='' ? "$v[extra] " : '')."/> $v[caption]</label>".$this->_break;
		    }
        
        }
		return $rtn;
	}
}

/**
 * Clase para la generacin y manejo de campos Yes/No (radios).
 */
class RMFormYesNo extends RMFormElement
{
	var $_value = '';
	/**
	 * @param string $caption Texto de la etiqueta
	 * @param string $name Nombre dle campo
	 * @param int $inicial Valor inicial (0 = No, 1 = S)
	 */
	public function __construct($caption, $name, $inicial=0){
		$this->setCaption($caption);
		$this->setName($name);
		$this->_value = isset($_REQUEST[$this->getName()]) ? $_REQUEST[$this->getName()] : $inicial;
	}
	/**
	 * Establece el valor incial del elemento
	 * @param int $value 0 = no, 1 = S
	 */
	public function setValue($value){
		$this->_value = $value;
	}
	/**
	 * Devuelve el valor inicial del elemento
	 * @return int
	 */
	public function getValue(){
		return $this->_value;
	}
	/**
	 * Genera el cdigo HTML para mostrar el campo
	 * @return string
	 */
	public function render(){
		$rtn = "<input name='".$this->getName()."' id='".$this->getName()."' type='radio' value='1' ";
		if ($this->_value==1){
			$rtn .= "checked='checked' ";
		}
		$rtn .= "/> ".__('Yes','rmcommon')."&nbsp;&nbsp;";
		$rtn .= "<input name='".$this->getName()."' id='".$this->getName()."' type='radio' value='0' ";
		if ($this->_value==0){
			$rtn .= "checked='checked' ";
		}
		$rtn .= "/> ".__('No','rmcommon');
		return $rtn;
	}
}

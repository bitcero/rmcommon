<?php
// $Id: hidden.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para el manejo y creación de cmapos HIDDEN
 */
class RMFormHidden extends RMFormElement
{
	var $_value;
	/**
	 * Constructor de la clase
	 * @param string $name Nombre del campo
	 * @param string $value Valor del elemento
	 */
	public function __construct($name, $value){
		$this->setName($name);
		$this->_value = $value;
	}
	/**
	 * Establece el valor del elemento.
	 * @param string $value Valor del elemento
	 */
	function setValue($value){
		$this->_value = $value;
	}
	/**
	 * Devuelve el valor del elemento
	 * @return string
	 */
	function getValue(){
		return $this->_value;
	}
	/**
	 * Genera el c?digo HTML para mostrar el elemento
	 * @return string
	 */
	function render(){
		$ret = '<input type="hidden" name="'.$this->getName().'" id="'.$this->id().'" value="'.$this->getValue().'" />';
		return $ret;
	}
}


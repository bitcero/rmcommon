<?php
// $Id: file.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------
 
/**
 * Clase para la creaci?n y manejo de elementos "FILE" para formularios
 * Esta clase se deriba de {@link EXMFormElement}
 */
class RMFormFile extends RMFormElement
{
	private $_size = 30;
	private $_limit = 0;
	/**
	 * Constructor de la clase
	 * @param string $caption Texto de la etiqueta
	 * @param string $name Nombre del campo
	 * @param int $size Longitud del campo (Por defecto 30)
     * @param int Limite en bytes para el tamaño del archivo
	 */
	public function __construct($caption, $name, $size=30, $limit=0){
		$this->_size = $size;
		$this->setCaption($caption);
		$this->setName($name);
		$this->_limit = $limit;
	}
	/**
	 * Modifica la longitud del campo
	 * @param int $size Longitud
	 */
	public function setSize($size){
		if ($size > 0){ $this->_size = $size; }
	}
	/**
	 * Devuelve la longitud actual del campo
	 * @return int
	 */
	public function getSize(){
		return $this->_size;
	}
	/**
	 * Genera el c?digo HTML para mostrar el campo
	 * @return string
	 */
	public function render(){
		$ret = '<input type="file" name="'.$this->getName().'" id="'.$this->id().'" size="'.$this->_size.'" class="form-control ';
		if ($this->getClass()!='')
			$ret .= $this->getClass();

		$ret .= '" ' . $this->getExtra().">";
		if ($this->_limit>0){
			$ret .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->_limit.'" />';
		}
		return $ret;
	}
}

?>
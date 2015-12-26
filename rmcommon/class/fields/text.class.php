<?php
// $Id: text.class.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para la creación de campos TEXT o PASSWORD
 */
class RMFormText extends RMFormElement
{
	var $_size = 30;
	var $_max;
	var $_value = '';
	var $_password = false;
	var $_enabled = true;
	
	/**
	 * Constructor de la clase
	 * @param string|array $caption Texto de la etiqueta
	 * @param string $name Nombre del campo
	 * @param int $size Longitud del campo
	 * @param int $max Longitud m?xima de car?cteres del campo
	 * @param string $value Valor por defecto
	 * @param bol $password True muestra un campo password
	 */
	function __construct($caption, $name = null, $size=10, $maxlength=64, $value='',$password=false, $enabled=true){

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('size', $size, 10);
            $this->setWithDefaults('maxlength', $maxlength, 64);
            $this->set('value', $value);
        }

        $this->setIfNotSet('type', 'text');
        $this->setIfNotSet('value', '');
	}

	/**
	 * Recupera la longitud del campo
	 * @return int
	 */
	public function getSize(){
        return (int) $this->get('size');
	}

	/**
	 * Recupera el n?mero de car?cteres
	 * @return int
	 */
	public function getMax(){
        return (int) $this->get('maxlength');
	}

    public function getValue($encoded = false){
        if($encoded){
            $value = htmlspecialchars($this->get('value', ''), ENT_QUOTES);
        } else{
            $value = $this->get('value', '');
        }
        return $value;
    }

	/**
	 * Devuelve el c?digo HTML para mostrar el campo.
	 * @return string
	 */
	public function render(){

        if($this->has('datalist')){
            $this->add('list', 'list_' . $this->getName());
        }

        $attributes = $this->renderAttributeString();

		return '<input ' . $attributes . ' ' . $this->getExtra() . '>';
	}
}

/**
 * Clase para la generación y manejo de subtitulos en la
 * tabla del formulario
 */
class RMFormSubTitle extends RMFormElement
{
	var $_type;
	/**
	 * @param string $caption Texto del subtitulo
	 * @param int $type 0 = th, 1 = td
	 * @param string $class solo si $type = 1
	 */
	public function __construct($caption, $type=0, $class='', $desc=''){
		$this->setName('');
		$this->setDescription($desc);
		$this->setCaption($caption);
		$this->_type = $type;
		$this->setClass($class);
	}
	/**
	 * Establece el tipo de titulo.
	 * 0 = TH, 1 = TD
	 * @param int $type Tipo de t?tulo
	 */
	public function setType($type){
		$this->_type = $type;
	}
	/**
	 * Devuelve el tipo de t?tulo
	 * @return int
	 */
	public function getType($type){
		return $this->_type;
	}
	/**
	 * Generamos el c?sigo HTML para crear el subt?tulo.
	 * @return string
	 */
	function render(){
            if($this->_type>0){
                $rtn = "<h4 class='form_subtitle ".$this->getClass()."'";
                foreach( $this->attrs as $name => $value ){

                    $rtn .= $name . '="' . $value . '" ';

                }
                $rtn .= ">".$this->getCaption()."</h4>";
            } else {
                $rtn = "<span class='".$this->getClass()."'";
                foreach( $this->attrs as $name => $value ){

                    $rtn .= $name . '="' . $value . '" ';

                }
                $rtn .= ">".$this->getCaption()."</span>";
            }
		return $rtn;
	}
}

/**
 * Clase para la generación y manejo de campos TEXTAREA
 */
class RMFormTextArea extends RMFormElement
{
	private $_rows = 4;
	private $_cols = 45;
	private $_value = '';
	private $width = '';
	private $height = '';
	
	/**
	 * @param string $caption Texto de la etiqueta
	 * @param string $name Nombre del campo
	 * @param int $rows N?mero de filas del campo
	 * @param int $cols N?mero de columnas del campo
	 * @param string $value Texto inicial del campo
	 * @param string $width Ancho del campo formateado para estilo CSS
	 * @param string $height Alto del campo formateado para estilo CSS
	 */
	public function __construct($caption, $name, $rows=4, $cols=45, $value='', $width='', $height=''){
		$this->_rows = $rows;
		$this->_cols = $cols;
		$this->_value = $value;
		$this->setCaption($caption);
		$this->setName($name);
		$this->width = $width;
		$this->height = $height;
        $this->addClass('form-control');
	}
	/**
	 * Establece el n?mero de filas del campo.
	 * @param int $rows N?mero de filas
	 */
	public function setRows($rows){
		$this->_rows = $rows;
	}
	/**
	 * Devuelve el n?mero de filas del campo
	 * @return int
	 */
	public function getRows(){
		return $this->_rows;
	}
	/**
	 * Establece el n?mero de columnas del campo
	 * @param int $cols N?mero de columnas
	 */
	public function setCols($cols){
		$this->_cols = $cols;
	}
	/**
	 * Devuelve el n?mero de columnas del campo
	 * @return int
	 */
	public function getCols(){
		return $this->_cols;
	}
	/**
	 * Establece el texto inicial del campo
	 * @param string $value Texto inicial
	 */
	function setValue($value){
		$this->_value = $value;
	}
	/**
	 * Devuelve el texto inicial del campo
	 * @return string
	 */
	function getValue(){
		return $this->_value;
	}
	/**
	 * Devuelve el código HTML para mostrar el campo
	 * @return string
	 */
	function render(){
		$ret = "<textarea name='".$this->getName()."' id='".$this->id()."' cols='".$this->_cols."' rows='".$this->_rows."' ";
		if ($this->getClass()!=''){
			$ret .= "class='".$this->getClass()."' ";
		}
		if ($this->width!='' || $this->height!=''){
			$ret .= "style='".($this->width!='' ? "width: $this->width; " : '').($this->height!='' ? "height: $this->height; " : '')."'";
		}
		$ret .= $this->getExtra();
        foreach( $this->attrs as $name => $value ){

            $ret .= $name . '="' . $value . '" ';

        }
        $ret .= ">".(isset($_REQUEST[$this->getName()]) ? $_REQUEST[$this->getName()] : $this->_value)."</textarea>";
		return $ret;
	}
}

/**
 * Clase para la generación de etiquetas
 */
class RMFormLabel extends RMFormElement
{
	/**
	 * @param string $caption Texto de la etiqueta
	 * @param string $cell Contenido de la celda
	 */
	public function __construct($caption, $cell, $id=''){
            $this->setCaption($caption);
            $this->setExtra($cell);
            $this->setName($id);
	}
	/**
	 * Genera el c?digo HTML para mostrar la etiqueta
	 * @return string
	 */
	function render(){
		return $this->getExtra();
	}
}

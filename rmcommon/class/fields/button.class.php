<?php
// $Id: button.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para crear botones de formlarios
 * {@link EXMFormElement}
 */
class RMFormButton extends RMFormElement
{
	private $_type = 'submit';
	private $_value = '';
	/**
	 * @param string $caption Nombre del elemento de formulario
	 * @param string $value Texto del Bot?n (Ej. Enviar, Cancelar)
	 * @param string $type Tipo de bot?n (Ej. submit, button). Por defecto el valor es 'submit'
	 */
	function __construct($caption, $value, $type = 'submit'){

        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, 'Caption Error');
            $this->setWithDefaults('type', $type, 'button');
        }

        $this->setIfNotSet('caption', 'Caption Error');
        $this->setIfNotSet('class', 'btn');
        $this->setIfNotSet('type', 'button');

        $this->suppressList[] = 'caption';
	}
	/**
	 * Establece o modifica el tipo del bot?n
	 * @param string $type Submit o Button
	 */
	public function setType($type){
		$this->_type = $type;
	}
	/**
	 * Recupera el tipo de bot?n
	 * @return string
	 */
	public function getType($type){
		return $this->_type;
	}
	/**
	 * Establece o modifica el texto del bot?n
	 * @param string $value Texto del bot?n
	 */
	public function setValue($value){
		$this->_value = $value;
	}
	/**
	 * Recupera el texto del bot?n
	 * @return string
	 */
	public function getValue(){
		return $this->_value;
	}
	/**
	 * Genera el c?digo HTML para mostrar el bot?n
	 * @return string C?digo HTML del Bot?n
	 */
	public function render(){
        $attributes = $this->renderAttributeString();
		$ret = "<button $attributes>" . $this->get('caption') . '</button>';
		return $ret;
	}
	
}

/**
 * Esta clase permite generar un grupo de botones
 * en una misma celda de una tabla de formulario
 */
class RMFormButtonGroup extends RMFormElement
{
	private $sep = '';
	private $buttons = array();
	private $ok = '';
	
	/**
	 * @param string $caption Texto de la celda
	 * @param string $separator Separador de botones. Puede ser cualquier car?cter (HTML)
	 */
	public function __construct($caption='&nbsp;',$separator=' '){
		$this->setCaption($caption);
		$this->sep = $separator;	
	}

    /**
     * Adds a new button to group
     *
     * @param string|object $name
     * @param $value
     * @param string $type
     * @param string $extra
     * @param bool $ok
     * @return bool
     */
	public function addButton($name, $value, $type = 'button', $extra='', $ok=false){

        if(is_a($name, 'RMFormButton')){
            $this->buttons[] = $name;
            return true;
        }

		$index = count($this->buttons);
		$this->buttons[$index] = new RMFormButton($value,'', $type);
		if (trim($extra)!='') $this->buttons[$index]->setExtra($extra);
		/**
		 * Si este boton se marca como "principal" ($ok=true)
		 * entonces tendr? una clase distinta
		 */
		if ($ok) $this->ok = $name;
	}
	/**
	 * Obtiene el array con los botones del grupo
	 * @return arrar Array de elementos EXMButton
	 */
	public function getButtons(){
		return $this->buttons;
	}
	/**
	 * Establece el contenido extra para un determinado bot?n.
	 * El par?metro pasado a esta funci?n puede contener c?digo JavaScript o
	 * cualquier otro dato que se desee pasar a la etiqueta <input .. />
	 * @param string $name Identificado del bot?n
	 * @param string $extra  Contenido a insertar
	 */
	public function setExtra($name, $extra){
		//if (!isset($this->buttons[$name])) return;
		foreach ($this->buttons as $index => $button){
			if ($button->getName()==$name){
				$this->buttons[$index]->setExtra($extra);
				break;
			}
		}
	}
	/**
	 * Genera el c?digo HTML para el grupo de botones.
	 * Lo que esta funci?n hace es llamar al m?todo render() de cada
	 * uno de los botones contenidos en el grupo
	 * @return string C?digo HTML
	 */
	public function render(){
		
		$ret = '';
		
		foreach ($this->buttons as $k => $button){
			
			if ($ret==''){
				$ret = $button->render();
			} else {
				$ret .= $this->sep . $button->render();
			}
		}
		
		return $ret;
	}
}

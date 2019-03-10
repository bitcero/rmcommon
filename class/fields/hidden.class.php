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
    /**
     * Constructor de la clase
     * @param string $name Nombre del campo
     * @param string $value Valor del elemento
     */
    public function __construct($name, $value)
    {
        if (is_array($name)) {
            parent::__construct($name);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('value', $value, '');
        }

        $this->setIfNotSet('id', $name);
    }

    /**
     * Establece el valor del elemento.
     * @param string $value Valor del elemento
     */
    public function setValue($value)
    {
        $this->set('value', $value);
    }

    /**
     * Devuelve el valor del elemento
     * @return string
     */
    public function getValue()
    {
        return $this->get('value');
    }

    /**
     * Genera el c?digo HTML para mostrar el elemento
     * @return string
     */
    public function render()
    {
        $attributes = $this->renderAttributeString();

        $ret = '<input type="hidden" ' . $attributes . '>';

        return $ret;
    }
}

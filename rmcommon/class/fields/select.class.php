<?php
// $Id: select.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para la generación de campos SELECT
 */
class RMFormSelect extends RMFormElement
{
    private $_rows = 5;
    private $_multi = 0;
    private $_options = array();
    private $_selected = null;
    private $_groups = array();
    private $current_group = '';

    /**
     * @param string $caption Texto de la etiqueta
     * @param string $name Nombre del elemento
     * @param int $multi Seleccion múltiple (0 = Inactivo, 1 = Activo)
     * @param array $selected Selected option
     */
    function __construct($caption, $name = '', $multi = 0, $selected = null)
    {

        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            if($multi){
                $this->set('multiple', null);
            }

            if(is_array($selected)){
                $this->set('value', $selected);
            }
        }

        $this->set('type', 'select');
        $this->setIfNotSet('value', []);
    }

    /**
     * Establece el número de filas del elemento.
     * Este valor es utilizado cuando {@link $_multi} esta establecido a 1
     * @param int $value Número de filas.
     */
    public function setRows($value)
    {
        $this->_rows = $value;
    }

    /**
     * Devuelve el número de filas del elemento
     * @return int
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Agrega una nueva opción al menú select.
     * Una nueva opción equivale a <option ...>...</option>.
     * @param mixed $value Valor de la opción
     * @param string $text Texto que mostrará la opción
     * @param int $select 1 selecciona por defecto la opción.
     * @param bol $disabled Mustra como inactiva esta opción.
     */
    public function addOption($value, $caption, $select = 0, $disabled = false, $style = '')
    {
        $options = $this->get('options');
        $options[$caption] = $value;
        $this->set('options', $options);
    }

    /**
     * @desc Agrega multiples elementos al campo select
     * @param array $options array de opciones
     */
    public function addOptionsArray($options)
    {
        foreach ($options as $k => $v) {
            $this->addOption($v, $k);
        }
    }

    /**
     * Establece una opción como seleccionada
     */
    public function setSelected($index)
    {
        foreach ($this->_options as $k => $v) {
            if ($v['value'] == $index) {
                $this->_options[$k]['select'] = 1;
                break;
            }
        }
    }

    /**
     * Devuelve el array de opciones del elemento.
     * @return array
     */
    function getOptions()
    {
        return $this->_options;
    }


    public function addGroup($label, $id)
    {

        if ('' == trim($id) || '' == trim($label))
            return false;

        $this->_groups[$id] = array('label' => $label, 'options' => array());
        $this->current_group = $id;

    }

    /**
     * Genera el código HTML para este elemento.
     * @return string
     */
    function render()
    {
        $this->setIfNotSet('class', 'form-control');
        $attributes = $this->renderAttributeString();
        $selected = $this->get('value');

        $selected = is_array($selected) ? $selected : [$selected];

        $rtn = "<select $attributes>";

        $options = $this->get('options');

        foreach ($options as $k => $v) {
            $rtn .= "<option value='$v'";
            if (is_array($selected) && in_array($v, $selected)) {
                $rtn .= " selected='selected'";
            }
            $rtn .= ">$k</option>";
        }

        // Check if there are Option Groups
        /*if (!empty($options)) {

            foreach ($this->_groups as $group) {
                $rtn .= '<optgroup label="' . $group['label'] . '">';

                foreach ($group['options'] as $option) {
                    $rtn .= "<option value='$option[value]'";
                    if ($option['select'] || (is_array($this->_selected) && in_array($option['value'], $this->_selected))) {
                        $rtn .= " selected='selected'";
                    }
                    if ($option['disabled']) {
                        $rtn .= " disabled='disabled'";
                    }
                    if ($option['style'] != '') $rtn .= " style='$option[style]'";
                    $rtn .= ">$option[text]</option>";
                }

                $rtn .= '</optgroup>';
            }

        }*/

        $rtn .= "</select>";
        return $rtn;
    }
}



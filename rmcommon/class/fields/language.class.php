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
    function __construct($caption, $name, $multi = 0, $type = 0, $selected = null, $cols = 2)
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            if ($multi) {
                $this->set('multiple', null);
            }

            if (is_array($selected)) {
                $this->set('selected', $selected);
            }
        }

        $this->setIfNotSet('type', $type ? 'radio' : 'select');
        $this->setIfNotSet('selected', []);

        $this->suppressList[] = 'value';

    }

    function multi()
    {
        return $this->multi;
    }

    function setMulti($value)
    {
        return $this->multi = $value;
    }

    function type()
    {
        return $this->type;
    }

    function setType($value)
    {
        return $this->type = $value;
    }

    function selected()
    {
        return $this->selected;
    }

    function setSelected($value)
    {
        return $this->selected = $value;
    }

    function render()
    {
        $files = XoopsLists::getFileListAsArray(XOOPS_ROOT_PATH . '/modules/rmcommon/lang', '');
        $langs = array();
        $langs['en_US'] = 'en';
        foreach ($files as $file => $v) {

            if (substr($file, -3) != '.mo') continue;

            $langs[substr($file, 0, -3)] = substr($file, 0, -3);

        }

        $type = $this->get('type');
        $selected = $this->get('selected');

        if ($type == 'radio' || $type == 'checkbox') {
            $rtn = '<div class="' . $type . '"><ul class="rmoptions_container">';
            $i = 1;

            if($type == 'checkbox'){
                $this->set('name', $this->get('name') . '[]');
            }
            $attributes = $this->renderAttributeString();

            foreach ($langs as $k) {
                $rtn .= "<li><label><input $attributes value='$k'" . (is_array($selected) ? (in_array($k, $selected) ? " checked" : '') : '') . "> $k</label></li>";
            }

            $rtn .= "</ul></div>";
        } else {
            $attributes = $this->renderAttributeString();
            $rtn = "<select $attributes>";
            foreach ($langs as $k) {
                $rtn .= "<option value='$k'" . (is_array($selected) ? (in_array($k, $selected) ? " selected='selected'" : '') : '') . ">$k</option>";
            }
            $rtn .= "</select>";
        }

        return $rtn;

    }
}

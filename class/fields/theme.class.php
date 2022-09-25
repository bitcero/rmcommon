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
     * @param mixed $section
     */
    public function __construct($caption, $name, $multi = 0, $type = 0, $selected = null, $cols = 2, $section = '')
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            if ($multi) {
                $this->setWithDefaults('multiple', null, null);
            }
            $this->setWithDefaults('type', 0 == $type ? 'select' : 'radio', 'select');
            $this->setWithDefaults('selected', $selected, []);
            if ('gui' == mb_strtolower($section)) {
                $this->set('gui', null);
            }
        }

        $this->setIfNotSet('type', 'select');
        $this->setIfNotSet('selected', []);

        //$this->suppressList[] = 'multiple';
        $this->suppressList[] = 'selected';
        $this->suppressList[] = 'gui';
    }

    public function render()
    {
        if ($this->has('gui')) {
            $dirs = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/modules/rmcommon/themes', '');
        } else {
            $dirs = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/themes', '');
        }

        $themes = [];

        $gui = $this->has('gui');
        $selected = $this->get('selected');
        $multiple = $this->has('multiple');

        foreach ($dirs as $dir => $v) {
            if ($gui) {
                if (!file_exists(XOOPS_ROOT_PATH . '/modules/rmcommon/themes/' . $dir . '/admin-gui.php')) {
                    continue;
                }
            } else {
                if (!file_exists(XOOPS_ROOT_PATH . '/themes/' . $dir . '/theme.html')) {
                    continue;
                }
            }

            // Read theme name
            $file = file_get_contents(XOOPS_ROOT_PATH . '/modules/rmcommon/themes/' . $dir . '/admin-gui.php');
            preg_match("/Theme name:\s{0,}(.*)?\n/m", $file, $name);
            $themes[$dir] = isset($name[1]) ? $name[1] : $dir;
        }

        unset($name);

        if ('checkbox' == $this->get('type') || 'radio' == $this->get('type')) {
            // Render attributes
            if ('checkbox' == $this->get('type')) {
                $this->set('name', $this->get('name') . '[]');
            }
            $attributes = $this->renderAttributeString();

            $rtn = '<ul class="rmoptions_container">';
            foreach ($themes as $k => $name) {
                $rtn .= "<li><label><input $attributes value='$k' " . (is_array($selected) ? (in_array($k, $selected, true) ? " checked" : '') : '') . "> $name</label></li>";
            }
            $rtn .= '</ul>';
        } else {
            $this->setIfNotSet('class', 'form-select');
            $attributes = $this->renderAttributeString();
            $rtn = "<select $attributes>";
            foreach ($themes as $k => $name) {
                $rtn .= "<option value='$k'" . (is_array($selected) ? (in_array($k, $selected, true) ? " selected='selected'" : '') : '') . ">$name</option>";
            }
            $rtn .= '</select>';
        }

        return $rtn;
    }
}

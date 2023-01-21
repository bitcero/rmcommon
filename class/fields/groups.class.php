<?php
// $Id: groups.class.php 928 2012-01-15 06:56:56Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para la creación de campos para el manejo de
 * grupos de usuarios XOOPS
 */
class RMFormGroups extends RMFormElement
{
    private $_multi = 0;
    private $_select = [];
    /**
     * Posibles valores
     * 0 = Select, 1 = Menu
     */
    private $_showtype = 0;
    private $_showdesc = 0;
    private $_cols = 2;

    /**
     * Constructor de la clase
     * @param mixed $caption Texto de la etiqueta
     * @param string $name Nombre del campo
     * @param mixed $multi
     * @param mixed $type
     * @param mixed $cols
     * @param mixed $selected
     */
    public function __construct($caption, $name = '', $multi = 0, $type = 0, $cols = 2, $selected = [])
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
            $this->setWithDefaults('type', 0 == $type ? 'select' : (1 == $multi ? 'checkbox' : 'radio'), 'select');
            $this->setWithDefaults('selected', $selected, []);
        }

        $this->setIfNotSet('type', 'select');
        $this->setIfNotSet('selected', []);
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));

        //$this->suppressList[] = 'multiple';
        $this->suppressList[] = 'selected';
        $this->suppressList[] = 'value';
        $this->suppressList[] = 'description';
    }

    /**
     * Establece el comportamiento de seleccion del campo groups.
     * Si $_multi = 0 entonces olo se puede seleccionar un grupo a la vez. En caso contrario
     * el campo permite la selección de múltiples grupos
     * @param int $value 1 o 2
     */
    public function setMulti($value)
    {
        if (0 == $value || 1 == $value) {
            $this->setName($value ? str_replace('[]', '', $this->getName()) . '[]' : str_replace('[]', '', $this->getName()));
            $this->_multi = $value;
        }
    }

    /**
     * Devuelve el valor multi del campo groups.
     * @return int
     */
    public function getMulti()
    {
        return $this->_multi;
    }

    /**
     * Indica los elementos seleccionados por defecto.
     * Este valor debe ser pasado como un array conteniendo los ideneitificadores
     * de los grupos (ej. array(0,1,2,3)) o bien como una lista delimitada por comas
     * conteniendo tambien los identificadores de grupos (ej, 1,2,3,4)
     * @param array $value Identificadores de los grupos
     * @param string $value Lista delimitada por comas con identificadores de los grupos
     */
    public function setSelect($value)
    {
        if (is_array($value)) {
            $this->_select = $value;
        } else {
            $this->_select = explode(',', $value);
        }
    }

    /**
     * Devuelve el array con los identificadores de los grupos
     * seleccionado por defecto.
     * @return array
     */
    public function getSelect()
    {
        return $this->_select;
    }

    /**
     * Establece la forma en que se mostrarán los grupos.
     * Esto puede ser en forma de lista o en forma de menu
     * @param int $value 0 ó 1
     */
    public function setShowType($value)
    {
        if (0 == $value || 1 == $value) {
            $this->_showtype = $value;
        }
    }

    /**
     * Devuelve el identificador de la forma en que se muestran los elementos
     * @return int
     */
    public function getShowType()
    {
        return $this->_showtype;
    }

    /**
     * Establece si se muestra la descripción de cada grupo o no.
     * Esta valor solo puede afectar cuando lso grupos se muestran
     * en forma de menu.
     * @param int $value 0 ó 1
     */
    public function showDesc($value)
    {
        if (0 == $value || 1 == $value) {
            $this->_showdesc = $value;
        }
    }

    /**
     * Devuelve si esta activa o no la opción para mostrar la descrpición de los grupos
     * @return int
     */
    public function getShowDesc()
    {
        return $this->_showdesc;
    }

    /**
     * Establece el número de columnas para el menu.
     * Cuando los grupos se mostrarán en forma de menú esta opción
     * permite especificar el número de columnas en las que se ordenarán.
     * @param int $value Número de columnas
     */
    public function setCols($value)
    {
        if ($value > 0) {
            $this->_cols = $value;
        }
    }

    /**
     * Devuelve el número de columnas del menú.
     * @return int
     */
    public function getCols()
    {
        return $this->_cols;
    }

    /**
     * Genera el código HTML para mostrar la lista o menú de grupos
     * @return string
     */
    public function render()
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $result = $db->query('SELECT * FROM ' . $db->prefix('groups') . ' ORDER BY `name`');
        $rtn = '';
        $col = 1;

        $typeinput = $this->get('type');
        $name = $this->getName();
        $selected = $this->get('selected');
        $selected = is_array($selected) ? $selected : [$selected];

        if ('radio' == $typeinput || 'checkbox' == $typeinput) {
            $this->remove('id');
            if ('checkbox' == $typeinput) {
                $this->set('name', $this->get('name') . '[]');
            }

            $attributes = $this->renderAttributeString();

            $rtn = "<ul class='groups_field_list " . $this->id() . "_groups'>";

            if ('checkbox' == $typeinput) {
                $rtn .= "<li><label><input $attributes value='0'";
                if (is_array($selected)) {
                    if (in_array(0, $selected, true)) {
                        $rtn .= ' checked';
                    }
                }
                $rtn .= '>' . __('All', 'rmcommon') . '</label></li>';
            }

            while (false !== ($row = $db->fetchArray($result))) {
                $rtn .= "<li><label><input $attributes value='$row[groupid]'";
                if (is_array($this->_select)) {
                    if (in_array($row['groupid'], $selected, true)) {
                        $rtn .= ' checked';
                    }
                }
                $rtn .= "> $row[name]</label>";

                if ($this->_showdesc) {
                    $rtn .= "<br><small style='font-size: 10px;' class='description'>$row[description]</small>";
                }

                $rtn .= '</li>';

                $col++;
            }
            $rtn .= '</ul>';
        } else {
            $this->setIfNotSet('class', 'form-control');
            $attributes = $this->renderAttributeString();
            $rtn = "<select $attributes\"><option value='0'";
            if (is_array($selected)) {
                if (in_array(0, $selected, true)) {
                    $rtn .= ' selected';
                }
            }

            $rtn .= '>' . __('Select...', 'rmcommon') . '</option>';

            while (false !== ($row = $db->fetchArray($result))) {
                $rtn .= "<option value='$row[groupid]'";
                if (in_array($row['groupid'], $selected, true)) {
                    $rtn .= ' selected';
                }
                $rtn .= '>' . $row['name'] . '</option>';
            }

            $rtn .= '</select>';
        }

        return $rtn;
    }
}

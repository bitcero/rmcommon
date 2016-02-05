<?php
/**
 * Common Utilities Frameworkf ro Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */


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
    function __construct($caption, $name = null, $size = 10, $maxlength = 0, $value = '', $password = false, $enabled = true)
    {

        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('size', $size, 10);
            if($maxlength > 0){
                $this->setWithDefaults('maxlength', $maxlength, 64);
            }
            $this->set('value', $value);
            if ($password) {
                $this->set('type', 'password');
            } else {
                $this->set('type', 'text');
            }
        }

        $this->setIfNotSet('type', 'text');
        $this->setIfNotSet('value', '');
    }

    /**
     * Recupera la longitud del campo
     * @return int
     */
    public function getSize()
    {
        return (int)$this->get('size');
    }

    /**
     * Recupera el n?mero de car?cteres
     * @return int
     */
    public function getMax()
    {
        return (int)$this->get('maxlength');
    }

    public function getValue($encoded = false)
    {
        if ($encoded) {
            $value = htmlspecialchars($this->get('value', ''), ENT_QUOTES);
        } else {
            $value = $this->get('value', '');
        }
        return $value;
    }

    /**
     * Devuelve el c?digo HTML para mostrar el campo.
     * @return string
     */
    public function render()
    {

        if ($this->has('datalist')) {
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
     * @param int $level Heading level for subtitle. Use '0' for no heading (span)
     * @param string $class solo si $type = 1
     * @param string $desc
     */
    public function __construct($caption, $level = 0, $class = '', $desc = '')
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('description', $desc, '');
            $this->setWithDefaults('level', $level, '');
        }

        $this->setIfNotSet('level', '3');
        $this->suppressList[] = 'caption';
        $this->suppressList[] = 'description';
    }

    /**
     * Establece el tipo de titulo.
     * 0 = TH, 1 = TD
     * @param int $type Tipo de t?tulo
     * @return null
     * @deprecated
     */
    public function setType($type)
    {
        return null;
    }

    /**
     * Devuelve el tipo de t?tulo
     * @return int
     * @deprecated
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Generamos el c?sigo HTML para crear el subt?tulo.
     * @return string
     */
    function render()
    {
        $attributes = $this->renderAttributeString();

        if ($this->get('level') > 0) {
            $rtn = "<h" . $this->get('level')." $attributes>";
            $rtn .= $this->get('caption');
            if($this->get('description')!=''){
                $rtn .= '<small>' . $this->get('description') . '</small>';
            }
            $rtn .= '</h' . $this->get('level') . '>';
        } else {
            $rtn = "<span $attributes>" . $this->get('caption') . "</span>";
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
    public function __construct($caption, $name = '', $rows = 4, $cols = 45, $value = '', $width = '', $height = '')
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('rows', $rows, 4);
            $this->setWithDefaults('cols', $cols, 45);
            $this->set('value', $value);
        }

        $this->setIfNotSet('rows', 4);
        $this->setIfNotSet('value', '');
    }

    /**
     * Establece el n?mero de filas del campo.
     * @param int $rows N?mero de filas
     */
    public function setRows($rows)
    {
        $this->_rows = $rows;
    }

    /**
     * Devuelve el n?mero de filas del campo
     * @return int
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * Establece el n?mero de columnas del campo
     * @param int $cols N?mero de columnas
     */
    public function setCols($cols)
    {
        $this->_cols = $cols;
    }

    /**
     * Devuelve el n?mero de columnas del campo
     * @return int
     */
    public function getCols()
    {
        return $this->_cols;
    }

    /**
     * Establece el texto inicial del campo
     * @param string $value Texto inicial
     */
    function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Devuelve el texto inicial del campo
     * @return string
     */
    function getValue()
    {
        return $this->_value;
    }

    /**
     * Devuelve el código HTML para mostrar el campo
     * @return string
     */
    function render()
    {
        $attributes = $this->renderAttributeString();

        $ret = "<textarea $attributes>" . (isset($_REQUEST[$this->get('name')]) ? $_REQUEST[$this->get('name')] : $this->get('value')) . "</textarea>";
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
    public function __construct($caption, $cell, $id = '')
    {
        $this->setCaption($caption);
        $this->setExtra($cell);
        $this->setName($id);
    }

    /**
     * Genera el c?digo HTML para mostrar la etiqueta
     * @return string
     */
    function render()
    {
        return $this->getExtra();
    }
}

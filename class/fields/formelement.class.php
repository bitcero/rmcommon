<?php
/**
 * Common Utilities Framework
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
 * @package      form
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

use Common\Core\Helpers\Attributes;

/**
 * Clase abstracta para derivar todos los elementos del formulario
 * Esta clase no puede ser instanciada directamente
 */
abstract class RMFormElement extends Attributes
{

    /**
     * @var string[] list of attributes to NOT render
     */
    protected $suppressList = ['caption', 'datalist', 'description', 'option', 'form'];

    private $extra = array();

    /**
     * __construct
     *
     * @param array $attributes array of attribute name => value pairs
     *                           Control attributes:
     *                               ElementFactory::FORM_KEY optional form or tray to hold this element
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
    }

    /**
     * render attributes as a string to include in HTML output
     *
     * @return string
     */
    public function renderAttributeString()
    {
        $this->suppressRender($this->suppressList);

        // title attribute needs to be generated if not already set
        if (!$this->has('title')) {
            $this->set('title', $this->getTitle());
        }

        // generate id from name if not already set
        if (!$this->has('id')) {
            $id = $this->get('name');
            if (substr($id, -2) === '[]') {
                $id = substr($id, 0, strlen($id) - 2);
            }
            $this->set('id', $id);
        }
        return parent::renderAttributeString();
    }

    /**
     * renderDatalist - get the datalist attribute for the element
     *
     * @return string "datalist" attribute value
     */
    public function renderDatalist()
    {
        if (!$this->isDatalist()) {
            return '';
        }
        $ret = "\n" . '<datalist id="list_' . $this->getName() . '">' . "\n";
        foreach ($this->get('datalist') as $datalist) {
            $ret .= '<option value="' . htmlspecialchars($datalist, ENT_QUOTES) . '">' . "\n";
        }
        $ret .= '</datalist>' . "\n";
        return $ret;
    }

    /**
     * Convenience method to assist with setting attributes when using BC Element syntax
     *
     * Set attribute $name to $value, replacing $value with $default if $value is empty, or if the
     * value is not one of the values specified in the (optional) $enum array
     *
     * @param string $name    attribute name
     * @param mixed  $value   attribute value
     * @param mixed  $default default value
     * @param array  $enum    optional list of valid values
     *
     * @return void
     */
    public function setWithDefaults($name, $value, $default = null, $enum = null)
    {
        if (empty($value)) {
            $value = $default;
        } elseif (null !== $enum && !in_array($value, $enum)) {
            $value = $default;
        }
        $this->set($name, $value);
    }

    /**
     * Sets the name of the element
     * @param string $name Name of the element
     * @return RMFormElement
     */
    public function setName($name)
    {

        $this->set('name', $name);

        return $this;
    }

    /**
     * Get the name of the element
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('name', '');
    }

    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * Get element unique id
     * @return string
     */
    public function id()
    {

        return $this->get('id', '');
    }

    /**
     * Sets the CSS class
     * @param string $class
     * @return RMFormElement
     */
    public function setClass($class)
    {
        $this->add('class', (string)$class);
        return $this;
    }

    public function addClass($class)
    {
        $this->add('class', (string)$class);
        return $this;
    }

    /**
     * Recupera el nombre de clase de un elemento espe?fico del formulario
     * @return string Nombre de la clase
     */
    public function getClass()
    {
        $class = $this->get('class', false);
        if ($class === false) {
            return false;
        }
        return htmlspecialchars(implode(' ', $class), ENT_QUOTES);
    }

    /**
     * Sets the caption of element
     * @param string $caption
     * @return RMFormElement
     */
    public function setCaption($caption)
    {
        $this->set('caption', $caption);
        return $this;
    }

    /**
     Gets the caption
     * @return string
     */
    public function getCaption()
    {
        return $this->get('caption', '');
    }

    /**
     * Description of element
     * @param string $description
     * @return RMFormElement
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
        return $this;
    }

    /**
     * Gets description
     * @param bool $encode
     * @return string
     */
    public function getDescription($encode = false)
    {
        $description = $this->get('description', '');
        return $encode ? htmlspecialchars($description, ENT_QUOTES) : $description;
    }

    /**
     * Add extra attributes to element
     *
     * Avoid the use of this method.
     *
     * @param string $extra Extra attribute to insert in element
     * @param bool $replace Add (true) or replace (false) current content
     *
     * @return RMFormElement
     *
     * @deprecated use the attributes on construct
     */
    public function setExtra($extra, $replace = false)
    {
        if ($replace) {
            $this->extra = array(trim($extra));
        } else {
            $this->extra[] = trim($extra);
        }

        return $this;
    }

    /**
     * Get the extra attributes of element
     *
     * @param bool $encode
     *
     * @return string
     *
     * @deprecated
     */
    public function getExtra( $encode = false )
    {
        if (!$encode) {
            return implode(' ', $this->extra);
        }
        $value = array();
        foreach ($this->extra as $val) {
            $value[] = str_replace('>', '&gt;', str_replace('<', '&lt;', $val));
        }
        return empty($value) ? '' : implode(' ', $value);
    }

    /**
     * @desc Asigna el formulario (nombre) al elemento actual
     */
    public function setForm($name)
    {
        $this->set('form', $name);
        return $this;
    }

    public function getForm()
    {
        return $this->get('form', '');
    }

    /**
     * Convenience method to assist with setting attributes when using BC Element syntax
     *
     * Set attribute $name to $value, replacing $value with $default if $value is empty, or if the
     * value is not one of the values specified in the (optional) $enum array
     *
     * @param string $name  attribute name
     * @param mixed  $value attribute value
     *
     * @return void
     */
    public function setIfNotEmpty($name, $value)
    {
        // don't overwrite
        if (!$this->has($name) && !empty($value)) {
            $this->set($name, $value);
        }
    }

    /**
     * Convenience method to assist with setting attributes
     *
     * Set attribute $name to $value, replacing $value with $default if $value is empty, or if the
     * value is not one of the values specified in the (optional) $enum array
     *
     * @param string $name  attribute name
     * @param mixed  $value attribute value
     *
     * @return void
     */
    public function setIfNotSet($name, $value)
    {
        // don't overwrite
        if (!$this->has($name)) {
            $this->set($name, $value);
        }
    }

    /**
     * setTitle - set the title for the element
     *
     * @param string $title title for element
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
    }

    /**
     * getTitle - get the title for the element
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->has('title')) {
            return $this->get('title');
        } else {
            if ($this->has(':pattern_description')) {
                return htmlspecialchars(
                    strip_tags($this->get('caption') . ' - ' . $this->get(':pattern_description')),
                    ENT_QUOTES
                );
            } else {
                return htmlspecialchars(strip_tags($this->get('caption')), ENT_QUOTES);
            }
        }
    }

    /**
     * Abstract method to be implemented on each field
     */
    abstract function render();
}

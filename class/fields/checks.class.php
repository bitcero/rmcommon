<?php
// $Id: checks.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * Clase para la creacin y manejo de campos CHECKBOX
 */
class RMFormCheck extends RMFormElement
{
  private $_options = [];

  /**
   * @param string|array $caption Texto de la etiqueta
   */
  public function __construct($caption)
  {
    if (is_array($caption)) {
      parent::__construct($caption);
    } else {
      parent::__construct([]);
      $this->setWithDefaults('caption', $caption, '');
      $this->set('display', 'list');
    }

    $this->set('type', 'checkbox');

    $this->suppressList[] = 'display';
    $this->suppressList[] = 'options';

    // User can provide options in constructor
    if ($this->has('options')) {
      foreach ($caption['options'] as $value => $option) {
        $this->_options[] = [
          'caption' => TextCleaner::getInstance()->clean_disabled_tags($option['caption']),
          'value' => TextCleaner::getInstance()->clean_disabled_tags($value),
          'selected' => $caption['selected'],
          'extra' => $caption['extra'],
          'name' => $caption['name'],
        ];
      }
    }
  }

  /**
   * Agrega una nueva casilla (checkbox) al elemento.
   * @param string $caption Texto de la casilla
   * @param string $name Nombre de la casilla
   * @param mixed $value Valor de la casilla
   * @param int $state Activada, descativada (1, 0)
   */
  public function addOption($caption, $name, $value, $state = 0)
  {
    $rtn = [];
    $rtn['caption'] = TextCleaner::getInstance()->clean_disabled_tags($caption);
    $rtn['value'] = TextCleaner::getInstance()->clean_disabled_tags($value);
    $rtn['selected'] = $state ? 'selected' : '';
    $rtn['name'] = $name;
    $this->_options[] = $rtn;
  }

  /**
   * Devuelve un array con las casilla del elemento.
   * @return array
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Genera el cdigo HTML necesario para mostrar el campo.
   * @return string
   */
  public function render()
  {
    $attributes = $this->renderAttributeString();

    $rtn = '';
    if ('inline' == $this->get('display')) {
      $rtn .= '';
      $cols = 1;
      foreach ($this->_options as $k => $v) {
        $rtn .= "<label class='checkbox-inline'><input $attributes name='" . $v['name'] . "[]' value='$v[value]' ";
        //if ($v['state']==1){
        $rtn .= RMHttpRequest::request($this->get('name')) == $v['value'] ? 'checked ' : ('selected' == $v['selected'] ? 'checked ' : '');
        //}
        $rtn .= "> $v[caption]</label>";
        $cols++;
      }
      $rtn .= '';
    } else {
      foreach ($this->_options as $k => $v) {
        $rtn .= "<div class='checkbox'><label><input $attributes name='$v[name]' value='$v[value]' ";
        //if ($v['state']==1){
        //	$rtn .= "checked ";
        //}
        $rtn .= RMHttpRequest::request($this->get('name'), 'string') == $v['value'] ? 'checked ' : ('selected' == $v['selected'] ? 'checked ' : '');
        $rtn .= "> $v[caption]</label></div>";
      }
    }

    return $rtn;
  }
}

/**
 * Clase para la creacin y manejo de campos RADIO
 */
class RMFormRadio extends RMFormElement
{
  private $_options = [];

  /**
   * @param string|array $caption Texto de la etiqueta.
   * @param string $name Nombre del campo.
   * @param int $inline Show inline controls or as list
   * @param int $type 1 = Tabla, 0 = Lista
   * @param int $cols Numero de columnas de la tabla
   */
  public function __construct($caption, $name = '', $inline = 0)
  {
    if (is_array($caption)) {
      parent::__construct($caption);
    } else {
      parent::__construct([]);
      $this->setWithDefaults('caption', $caption, '');
      $this->setWithDefaults('name', $name, 'name_error');
      $this->setWithDefaults('display', $inline ? 'inline' : 'list', 'list');
    }

    $this->set('type', 'radio');

    $this->suppressList[] = 'display';
    $this->suppressList[] = 'options';
    $this->suppressList[] = 'value';

    // User can provide options in constructor
    if (is_array($caption) && array_key_exists('options', $caption)) {
      foreach ($caption['options'] as $value => $option) {
        if (is_array($option)) {
          $this->_options[] = [
            'caption' => TextCleaner::getInstance()->clean_disabled_tags($option['caption']),
            'value' => TextCleaner::getInstance()->clean_disabled_tags($value),
            'selected' => $caption['selected'],
            'extra' => $caption['extra'],
          ];
        } else {
          $this->_options[] = [
            'caption' => TextCleaner::getInstance()->clean_disabled_tags($option),
            'value' => TextCleaner::getInstance()->clean_disabled_tags($value),
            'selected' => '',
            'extra' => '',
          ];
        }
      }
    }
  }

  /**
   * Agrega una nueva opcion (radio) al elemento
   * @param string $caption Texto de la etiqueta
   * @param mixed $value valor del elemento
   * @param int $state 0 Desactivado, 1 Activado
   * @param mixed $extra
   */
  public function addOption($caption, $value, $state = 0, $extra = '')
  {
    $rtn = [];
    $rtn['caption'] = TextCleaner::getInstance()->clean_disabled_tags($caption);
    $rtn['value'] = TextCleaner::getInstance()->clean_disabled_tags($value);
    $rtn['selected'] = $state;
    $rtn['extra'] = $extra;
    $this->_options[] = $rtn;
  }

  /**
   * Devuelve el array con las opciones (radios) del elemento.
   * @return array
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Genera el cdigo HTML para mostrar el elemento
   * @return string
   */
  public function render()
  {
    $rtn = '';

    $attributes = $this->renderAttributeString();

    if ('inline' == $this->get('display')) {
      foreach ($this->_options as $k => $v) {
        $rtn .= '<label class="radio-inline">';
        $rtn .= "<input $attributes value='$v[value]' ";
        $rtn .= RMHttpRequest::request($this->get('name'), 'string', '') == $v['value'] ? 'checked ' : ($v['value'] == $this->get('value') ? "checked " : '');
        $rtn .= ('' != $v['extra'] ? "$v[extra] " : '') . "> $v[caption]</label>";
      }
    } else {
      foreach ($this->_options as $k => $v) {
        $rtn .= "<div class=\"radio\"><label><input $attributes value='$v[value]' ";
        $rtn .= RMHttpRequest::request($this->get('name', 'string', ''), 'mixed') == $v['value'] ? 'checked ' : ($v['value'] == $this->get('value') ? ' checked ' : '');
        $rtn .= ('' != $v['extra'] ? "$v[extra] " : '') . "> $v[caption]</label></div>";
      }
    }

    return $rtn;
  }
}

/**
 * Clase para la generacin y manejo de campos Yes/No (radios).
 */
class RMFormYesNo extends RMFormElement
{
  /**
   * @param string $caption
   * @param string $name
   * @param int $value Initial value (0 = No, 1 = S)
   */
  public function __construct($caption, $name = '', $value = 0)
  {
    if (is_array($caption)) {
      parent::__construct($caption);
    } else {
      parent::__construct([]);
      $this->setWithDefaults('caption', $caption, '');
      $this->setWithDefaults('name', $name, 'name_error');
      $this->setWithDefaults('value', $value, 0);
    }

    $this->suppressList[] = 'value';
    $this->suppressList[] = 'type';
  }

  /**
   * Establece el valor incial del elemento
   * @param int $value 0 = no, 1 = S
   */
  public function setValue($value)
  {
    $this->set('value', $value);
  }

  /**
   * Devuelve el valor inicial del elemento
   * @return int
   */
  public function getValue()
  {
    return $this->get('value');
  }

  /**
   * Genera el cdigo HTML para mostrar el campo
   * @return string
   */
  public function render()
  {
    global $common;
    $attributes = $this->renderAttributeString();
    $common->template()->assign('attributes', $attributes);
    $common->template()->assign('name', $this->get('name'));
    $common->template()->assign('id', $this->get('id'));
    $common->template()->assign('value', $this->get('value'));

    ob_start();
    include $common->template()->path('fields/yesno.php', 'module', 'rmcommon');
    return ob_get_clean();
  }
}

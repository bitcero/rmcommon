<?php
// $Id: text.class.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormRewrite extends RMFormElement
{
  private $default = '';

  public function __construct($caption, $name, $default = [])
  {
    $this->setCaption($caption);
    $this->setName($name);

    $this->default = $default;
  }

  public function id()
  {
    return TextCleaner::getInstance()->sweetstring($this->getName());
  }

  public function render()
  {
    /**
     * Load all modules that supports rewrite feature
     */
    $moduleHandler = xoops_getHandler('module');
    $objects = $moduleHandler->getObjects();
    $modules = [];

    foreach ($objects as $mod) {
      if (!$mod->getInfo('rewrite') || 'rmcommon' == $mod->getVar('dirname')) {
        continue;
      }

      $modules[] = $mod;
    }

    unset($objects, $mod);

    ob_start();
    require RMTemplate::get()->get_template('fields/field-rewrite.php', 'module', 'rmcommon');
    $field = ob_get_clean();

    return $field;
  }
}

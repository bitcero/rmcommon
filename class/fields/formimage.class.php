<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Esta clase crea un campo para imágen utilizando
* el administrador de imágenes de Common Utilities
*/
class RMFormImage extends RMFormElement
{
    private $default = '';
    
    public function __construct($caption, $name, $default=''){
        
        $this->setCaption($caption);
        $this->setName($name);
        $this->default = $default;
        
        !defined('RM_FRAME_USERS_CREATED') ? define('RM_FRAME_USERS_CREATED', 1) : '';
    }
    
    public function render(){
        
        $util = RMUtilities::get();
        return $util->image_manager($this->getName(), $this->id(), $this->default);
        
    }
}

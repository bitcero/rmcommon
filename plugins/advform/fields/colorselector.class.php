<?php
// $Id: colorselector.class.php 11036 2013-02-12 04:03:30Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormColorSelector extends RMFormElement
{
    private $initial = '';
    private $sharp = false;
    
    public function __construct($caption, $name, $initial, $addsharp = false){
        
        $this->setCaption($caption);
        $this->setName($name);
        $this->sharp = $addsharp;
        
        if($addsharp && $initial!=''){
            if(!preg_match("/^#[a-f0-9]{1,}$/is", $initial))
                $this->initial = '#'.$initial;
            else
                $this->initial = $initial;
        } else {
            $this->initial = str_replace('#', '', $initial);
        }

    }
    
    public function render(){
        global $rmTpl;
        
        $rmTpl->add_style("colorpicker.css", 'rmcommon');
        $rmTpl->add_local_script("colorpicker.js", 'rmcommon', 'include');
        
        $ret = '<div class="input-group adv-color-chooser" id="adv-color-'.$this->id().'">';
        $ret .= '<span class="input-group-addon previewer" style="background-color: '.(!$this->sharp ? '#'.$this->initial : $this->initial).';">&nbsp;</span>';
        $ret .= '<input class="form-control" '.($this->sharp ? 'data="#"' : '').' type="text" name="'.$this->getName().'" id="'.$this->id().'" value="'.($this->initial!='' ? $this->initial : ($this->sharp ? '#' : '').'FFF').'" />';
        $ret .= '<span class="input-group-addon chooser">...</span>';
        $ret .= '</div>';
        
        return $ret;
        
    }
    
}

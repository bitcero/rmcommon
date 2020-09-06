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
    
    public function __construct($caption, $name = '', $initial = '#FFFFFF', $addsharp = true){

        $this->suppressList[] = 'initial';
        $this->suppressList[] = 'hash';
        $this->suppressList[] = 'id';

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, '');
            $this->setWithDefaults('value', $initial, '');
            $this->setWithDefaults('hash', $addsharp, false);
        }

        $this->setIfNotSet('hash', true);
        
        $this->addClass('input-group adv-color-chooser');
        $this->setIfNotSet('value', '#FFFFFF');
        $initial = $this->get('value');
        
        if($this->get('hash') && $initial!=''){
            if(!preg_match("/^#[a-f0-9]{1,}$/is", $initial) && $initial != 'transparent' && 'rgb' != substr($initial, 0, 3))
                $this->set('value', '#'.$initial);
            else
                $this->set('value', $initial);
        } else {
            $this->set('value', str_replace('#', '', $initial));
        }

        $this->suppressList[] = 'hash';
        $this->suppressList[] = 'value';

    }
    
    public function render(){
        global $rmTpl;

        $attributes = $this->renderAttributeString();

        $rmTpl->add_style("colorpicker.css", 'rmcommon');
        $rmTpl->add_script("colorpicker.js", 'rmcommon');
        
        $ret = '<div '.$attributes.' id="adv-color-'.$this->get('id').'">';
        $ret .= '<span class="input-group-addon previewer" style="background-color: '.(!$this->get('hash') ? '#'.$this->get('value') : $this->get('value')).';">&nbsp;</span>';
        $ret .= '<input class="form-control" '.($this->get('hash') ? 'data="#"' : '').' type="text" name="'.$this->get('name').'" id="'.$this->get('id').'" value="'.($this->get('value')!='' ? $this->get('value') : ($this->get('hash') ? '#' : '').'FFF').'" />';
        $ret .= '<span class="input-group-btn chooser"><button type="button" class="btn btn-default">...</button></span>';
        $ret .= '</div>';
        
        return $ret;
        
    }
    
}

<?php
// $Id: colorselector.class.php 11036 2013-02-12 04:03:30Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormImageSelect extends RMFormElement
{
    private $initial = '';
    private $width = 50;
    private $height = 50;
    private $images = array();

    /**
     * @param string Caption of field
     * @param string Name of form field
     * @param string Image slected
     * @param int Width of image thumbnail
     * @param int Height of image thumbnail
     */
    public function __construct($caption, $name, $initial, $width = 50, $height = 50){
        
        $this->setCaption($caption);
        $this->setName($name);
        $this->initial = $initial;

    }

    /**
     * Add images to the control
     * @param mixed value
     * @param string image url
     */
    public function addImage($value, $image){

        $this->images[] = array('value' => $value, 'image' => $image);

    }

    public function render(){
        global $rmTpl;

        
        $ret = '<div class="adv-images-select" id="adv-imgsel-'.$this->id().'">';

        foreach($this->images as $img){
            $v = $img['value'];
            $url = $img['image'];
            $ret .= '<label style="width: '.$this->width.'px; height: '.$this->height.'px;"><input type="radio" name="'.$this->getName().'" value="'.$v.'"'.($v==$this->initial ? ' checked="checked"' : '').'>';
            $ret .= '<span style="background-image: url('.$url.')" title="'.$v.'"></span></label>';
        }

        $ret .= '</div>';
        
        return $ret;
        
    }
    
}

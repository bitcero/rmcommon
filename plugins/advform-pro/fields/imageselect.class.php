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
    private $accepted = array(
        'jpg','gif','png','bmp','svg'
    );

    /**
     * @param string Caption of field
     * @param string Name of form field
     * @param string Image slected
     * @param int Width of image thumbnail
     * @param int Height of image thumbnail
     */
    public function __construct($caption, $name = '', $initial = '', $width = 50, $height = 50){

        $this->suppressList[] = 'initial';
        $this->suppressList[] = 'width';
        $this->suppressList[] = 'height';
        $this->suppressList[] = 'id';
        $this->suppressList[] = 'images';

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('value', $initial, '');
            $this->setWithDefaults('width', $width, 50);
            $this->setWithDefaults('height', $height, 50);
        }

        $this->setIfNotSet('value', $initial);
        $this->setIfNotSet('width', 50);
        $this->setIfNotSet('height', 50);
        $this->setIfNotSet('images', []);

        $this->addClass('adv-images-select');

    }

    /**
     * Add images to the control
     * @param mixed value
     * @param string image url
     */
    public function addImage($value, $image){

        $images = $this->get('images');

        $this->images[] = array('value' => $value, 'image' => $image);
        $this->set('images', $images);

    }

    public function render(){
        global $rmTpl;

        $attributes = $this->renderAttributeString();
        
        $ret = '<div '. $attributes . ' id="adv-imgsel-'.$this->get('id').'">';

        $ret .= '<label style="width: '.$this->get('width').'px; height: '.$this->get('height').'px;"><input type="radio" name="'.$this->get('name').'" value=""'.(''==$this->get('default') ? ' checked="checked"' : '').'>';
        $ret .= '<span title="'.__('None', 'advform-pro').'"></span></label>';

        $images = $this->get('images');

        foreach($images as $img){

            $v = $img['value'];
            $url = $img['image'];

            $data = array_reverse( explode( ".", str_replace( dirname( $url ) . '/', '', $url ) ) );
            if ( count( $data ) < 2 )
                continue;

            if ( !in_array( $data[0], $this->accepted ) )
                continue;

            $ret .= '<label style="width: '.$this->get('width').'px; height: '.$this->get('height').'px;"><input type="radio" name="'.$this->get('name').'" value="'.$v.'"'.($v==$this->get('value') ? ' checked="checked"' : '').'>';
            $ret .= '<span style="background-image: url('.$url.')" title="'.$v.'"></span></label>';
        }

        $ret .= '</div>';
        
        return $ret;
        
    }
    
}

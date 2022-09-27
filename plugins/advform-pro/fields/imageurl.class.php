<?php
// $Id: imageurl.class.php 11036 2013-02-12 04:03:30Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormImageUrl extends RMFormElement
{
    private $default = '';
    
    public function __construct($caption, $name = '', $value = ''){

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('value', $value, '');
        }

        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));
        $this->setIfNotSet('class', 'form-control');

    }
    
    public function render(){

        $attrs = $this->renderAttributeString();

        RMTemplate::getInstance()->add_inline_script('var imgmgr_title = "'.__('Image Manager','rmcommon').'"'."\n".'var mgrURL = "'.RMCURL.'/include/tiny-images.php";', 1);
        
        $ret = '<div class="adv_imgurl" id="iurl-container-'.$this->get('id').'"><div class="input-group txt-and-button">';
        $ret .= '<input type="text" ' . $attrs . '>';
        $ret .= '<span class="input-group-btn adv_img_launcher" data-id="'.$this->get('id').'" data-title="'.__('Insert Image URL','advform-pro').'">
        <button type="button" class="btn btn-primary">...</button></span>';
        $ret .= '</div>';
        $ret .= '<div class="img-preview"><img id="preview-'.$this->get('id').'" src="'.$this->get('value').'"'.($this->get('value')!='' ? ' style="display: inline-block;"' : '').' /></div></div>';
        return $ret;
    }
    
}

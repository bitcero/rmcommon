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
    
    public function __construct($caption, $name, $value = ''){
        
        $this->setCaption($caption);
        $this->setName($name);
        $this->default = $value;
        
    }
    
    public function render(){
        global $rmTpl;
        
        $rmTpl->add_head_script('var imgmgr_title = "'.__('Image Manager','rmcommon').'"'."\n".'var mgrURL = "'.RMCURL.'/include/tiny-images.php";');
        
        $ret = '<div class="adv_imgurl" id="iurl-container-'.$this->id().'"><div class="input-group txt-and-button">';
        $ret .= '<input class="form-control" type="text" name="'.$this->getName().'" id="'.$this->id().'" value="'.$this->default.'" size="10" />';
        $ret .= '<span class="input-group-addon adv_img_launcher" data-id="'.$this->id().'" data-title="'.__('Insert Image URL','advform').'">...</span>';
        $ret .= '</div>';
        $ret .= '<div class="img-preview"><img id="preview-'.$this->id().'" src="'.$this->default.'"'.($this->default!='' ? ' style="display: inline-block;"' : '').' /></div></div>';
        return $ret;
    }
    
}

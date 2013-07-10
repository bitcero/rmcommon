<?php
// $Id: lightbox-plugin.php 838 2011-12-10 19:06:27Z i.bitcero $
// --------------------------------------------------------------
// LightBox plugin for Common Utilities
// Integrate jQuery LightBox with Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class LightBoxCUPlugin extends RMIPlugin
{
	public function __construct(){
        
        // Load language
        load_plugin_locale('lightbox', '', 'rmcommon');
        
        $this->info = array(
            'name'            => __('LighBox Plugin', 'lightbox'),
            'description'    => __('This plugin allows to use jQuery LightBox in modules and other elements.','lightbox'),
            'version'        => '1.0.3.9',
            'author'        => 'Eduardo Cortés',
            'email'            => 'i.bitcero@gmail.com',
            'web'            => 'http://redmexico.com.mx',
            'dir'            => 'lightbox'
        );
        
    }
    
    public function on_install(){
        return true;
    }
    
    public function on_uninstall(){
        return true;
    }
    
    public function on_update(){
        return true;
    }
    
    public function on_activate($q){
        return true;
    }
    
    public function options(){
        
        require 'include/options.php';
        return $options;
        
    }
}
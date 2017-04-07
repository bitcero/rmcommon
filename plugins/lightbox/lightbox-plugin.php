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
            'name'          => __('Lightbox Plugin', 'lightbox'),
            'description'   => __('This plugin allows to use jQuery Colorbox in modules and other elements.','lightbox'),
            'version'       => array('major' => 1, 'minor' => 12, 'stage' => 0, 'name' => 'Lightbox Plugin'),
            'author'        => 'Eduardo Cortés',
            'email'         => 'i.bitcero@gmail.com',
            'web'           => 'http://eduardocortes.mx',
            'dir'           => 'lightbox',
            //'updateurl'     => 'https://www.xoopsmexico.net/modules/vcontrol/',
            'hasmain'       => true
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

    static function getInstance(){
        static $instance;

        if(!isset($instance)){
            $instance = new LightBoxCUPlugin();
        }

        return $instance;
    }
}
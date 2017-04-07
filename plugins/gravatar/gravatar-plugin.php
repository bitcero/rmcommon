<?php
// $Id: avatars-plugin.php 326 2010-04-28 19:28:52Z i.bitcero $
// --------------------------------------------------------------
// Avatars plugin for Common Utilities
// Allows to integrate gravatars and other systems in Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class GravatarCUPlugin extends RMIPlugin
{
    public function __construct(){
        
        // Load language
        load_plugin_locale('gravatar', '', 'rmcommon');
        
        $this->info = array(
            'name'            => __('Gravatar for XOOPS', 'avatars'),
            'description'    => __('Plugin to use gravatar in XOOPS with Common Utilities','avatars'),
            'version'        => array('major'=>1,'minor'=>2,'revision'=>18, 'stage'=>0,'name'=>'Gravatars'),
            'author'        => 'Eduardo Cortés',
            'email'            => 'i.bitcero@gmail.com',
            'web'            => 'http://eduardocortes.mx',
            'dir'            => 'gravatar',
            //'updateurl'     => 'https://www.xoopsmexico.net/modules/vcontrol/'
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
            $instance = new GravatarCUPlugin();
        }

        return $instance;
    }
    
}

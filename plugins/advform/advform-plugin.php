<?php
// $Id: advform-plugin.php 11036 2013-02-12 04:03:30Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class AdvformCUPlugin extends RMIPlugin
{
    
    public function __construct(){
        load_plugin_locale('advform', '', 'rmcommon');
        
        $this->info = array(
            'name'          => __('AdvancedForms Plugin', 'advform'),
            'description'   => __('Improves rmcommon forms by addign new fields and controls','advform'),
            'version'       => array('major'=>0,'minor'=>2,'revision'=>34, 'stage'=>-2,'name'=>'AdvancedForms'),
            'author'        => 'Eduardo Cortes',
            'email'         => 'i.bitcero@gmail.com',
            'web'           => 'http://www.redmexico.com.mx',
            'dir'           => 'advform',
            'updateurl'     => 'http://www.xoopsmexico.net/modules/vcontrol/?action=check&id=10'
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

        //require 'include/options.php';
        return $options;

    }
    
    
}

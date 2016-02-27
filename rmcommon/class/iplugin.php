<?php
// $Id: iplugin.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Interface for Common Utilities plugins
*/

abstract class RMIPlugin
{
    protected $info = array();
    protected $settings = array();
    protected $errors = array();

    public function on_install(){return true;}
    public function on_uninstall(){return true;}
    public function on_update(){return true;}
    public function on_activate($q){return true;}
    public function options(){return array();}

    function get_info($name){

        if (!isset($this->info[$name])) return '';
        return $this->info[$name];

    }

    public function info(){
        return $this->info;
    }

    public function settings($name=''){

        $settings = empty($this->settings) ? RMFunctions::get()->plugin_settings($this->get_info('dir'), true) : $this->settings;

        if(isset($settings[$name]))

            return $settings[$name];

        return $settings;

    }

    public function addError( $error_string ){

        $this->errors[] = $error_string;

    }

    public function errors( $lines = true ){

        if ( $lines )
            return implode( "<br>", $this->errors );
        else
            return $this->errors;

    }

    public function path(){
        return RMCPATH . '/plugins/' . $this->get_info('dir');
    }

    public function url(){
        return RMCURL . '/plugins/' . $this->get_info('dir');
    }

    abstract static function getInstance();

}

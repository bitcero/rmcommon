<?php
// $Id: text.class.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


class RMFormRewrite extends RMFormElement
{

    private $default = '';

    public function __construct( $caption, $name, $default = array() ){
        $this->setCaption( $caption );
        $this->setName( $name );

        $this->default = $default;
    }

    public function id(){
        return TextCleaner::getInstance()->sweetstring( $this->getName() );

    }

    public function render(){

        /**
         * Load all modules that supports rewrite feature
         */
        $module_handler = xoops_gethandler('module');
        $objects = $module_handler->getObjects();
        $modules = array();

        foreach ($objects as $mod) {

            if( !$mod->getInfo('rewrite') || $mod->getVar('dirname') == 'rmcommon' )
                continue;

            $modules[] = $mod;

        }

        unset( $objects, $mod );

        ob_start();
        require RMTemplate::get()->get_template( 'fields/field-rewrite.php', 'module', 'rmcommon' );
        $field = ob_get_clean();

        return $field;

    }

}

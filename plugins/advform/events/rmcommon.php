<?php
// $Id: rmcommon.php 11044 2013-02-13 04:54:14Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class AdvformPluginRmcommonPreload{
    
    public function eventRmcommonFormLoader(){
        global $rmTpl;
        
        if(defined('ADVF_INCLUDED')) return;
        
        define('ADVF_INCLUDED', 1);
        $path = RMCPATH.'/plugins/advform/fields/';

        include $path.'webfonts.class.php';
        include $path.'imageurl.class.php';
        include $path.'slider.class.php';
        include $path.'colorselector.class.php';
        include $path.'imageselect.class.php';

        $rmTpl->add_script('load-script.php?script=webfonts', 'rmcommon', array('directory' => 'plugins/advform'));
        $rmTpl->add_script('advanced-fields.js', 'rmcommon', array('directory' => 'plugins/advform'));
        $rmTpl->add_style('advforms.css', 'rmcommon', array('directory' => 'plugins/advform'));
        $rmTpl->add_head_script(include_once(RMCPATH.'/plugins/advform/js/adv-lang.php'));
        
    }
    
}

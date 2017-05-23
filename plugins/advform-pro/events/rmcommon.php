<?php
/**
 * Advanced Form Fields Pro for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      advform-pro
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

class AdvformproPluginRmcommonPreload{
    
    static function eventRmcommonFormLoader(){
        global $rmTpl;

        if(defined('ADVF_INCLUDED')) return;

        define('ADVF_INCLUDED', 1);
        $path = RMCPATH.'/plugins/advform-pro/fields/';

        include $path.'webfonts.class.php';
        include $path.'imageurl.class.php';
        include $path.'slider.class.php';
        include $path.'colorselector.class.php';
        include $path.'imageselect.class.php';
        include $path.'iconpicker.class.php';
        include $path.'select.class.php';
        include $path.'icons.class.php';
        include $path.'countries.class.php';
        include $path.'states.class.php';
        include $path.'repeater.class.php';

        $rmTpl->add_script('load-script.php?script=webfonts', 'rmcommon', array('footer' => 1, 'directory' => 'plugins/advform-pro'));
        $rmTpl->add_script('advanced-fields.min.js', 'rmcommon', array('footer' => 1, 'directory' => 'plugins/advform-pro', 'id' => 'advform-js'));
        $rmTpl->add_style('advforms.min.css', 'rmcommon', array('directory' => 'plugins/advform-pro'));
        $rmTpl->add_head_script(include_once(RMCPATH.'/plugins/advform-pro/js/adv-lang.php'));

    }

    static function eventRmcommonLoadFormField($ele, $field){

        $plugin = Common\Core\Helpers\Plugins::getInstance()->load('advform-pro');
        $new = $plugin->renderLocalField($field);

        if(null == $new){
            return $ele;
        } else {
            return $new;
        }

    }

    public function eventRmcommonPsr4loader($loader)
    {
        $loader->addNamespace('Common\Plugins\AdvForm', XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/advform-pro/fields');
        return $loader;
    }
    
}

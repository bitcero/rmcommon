<?php
/**
 * Polyglot for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
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
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      polyglot
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

global $common, $xoopsSecurity;

$action = $common->httpRequest()->post('action', 'string', '');

$languages = json_decode(file_get_contents($this->file), true);

/*
 * Save languages data
 */
if('save' == $action){

    $langs = $common->httpRequest()->post('langs', 'array', []);

    foreach($langs as $code => $lang){

        if(!array_key_exists($code, $languages)){
            continue;
        }

        if(array_key_exists('charset', $lang) && '' != $lang['charset']){
            $languages[$code]['charset'] = $lang['charset'];
        } else {
            $languages[$code]['charset'] = 'UTF-8';
        }

        if(array_key_exists('rtl', $lang) && 'rtl' == $lang['rtl']){
            $languages[$code]['rtl'] = $lang['rtl'];
        } else {
            unset($languages[$code]['rtl']);
        }

        file_put_contents($this->file, json_encode($languages));

    }

}

$common->template()->add_attribute('html', [
    'class' => 'polyglot-dashboard'
]);

$common->template()->add_style('polyglot-dashboard.min.css', 'rmcommon', [
    'directory' => 'plugins/polyglot',
    'id' => 'polyglot-ds-css'
]);

// Language
include $common->path('plugins/polyglot/js/polyglot-lang.php');

$common->template()->add_script('polyglot-dashboard.min.js', 'rmcommon', [
    'directory' => 'plugins/polyglot',
    'footer' => 1,
    'id' => 'polyglot-js'
]);

$common->breadcrumb()->add_crumb(__('Plugins Manager', 'polyglot'), 'plugins.php');
$common->breadcrumb()->add_crumb(__('Polyglot', 'polyglot'), 'plugins.php?p=polyglot');
$common->breadcrumb()->add_crumb(__('Adjustments', 'polyglot'));

$common->template()->header();

$common->template()->assign('languages', $languages);
$common->template()->assign('baseAssigned', $baseAssigned);
$common->template()->assign('plugin', $this);
$common->template()->assign('page', 'adjusts');
$common->template()->assign('file', 'polyglot-adjusts.php');

$common->template()->header();

$common->template()->display('polyglot-dashboard.php', 'plugin', 'rmcommon', 'polyglot');

$common->template()->footer();

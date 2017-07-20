<?php
/**
 * Advanced Form Fields for Common Utilities
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

class AdvformproCUPlugin extends RMIPlugin
{

    public function __construct()
    {

        load_plugin_locale('advform-pro', '', 'rmcommon');

        $this->info = array(
            'name' => __('Advanced Forms Fields Pro', 'advform-pro'),
            'description' => __('Improves rmcommon forms by addign new fields and controls', 'advform-pro'),
            'version' => array('major' => 1, 'minor' => 0, 'revision' => 15, 'stage' => 0, 'name' => 'AdvancedForms'),
            'author' => 'Eduardo Cortes (AKA bitcero)',
            'email' => 'i.bitcero@gmail.com',
            'web' => 'http://www.redmexico.com.mx',
            'dir' => 'advform-pro',
            'updateurl' => 'https://sys.eduardocortes.mx/updates/',
            'hasmain' => true
        );

        include_once 'includes/js-lang.php';

    }

    public function on_install()
    {
        $module = RMModules::load_module('rmcommon');

        if(version_compare('2.3', RMFormat::version($module->getInfo('rmversion'), false)) > 0){
            $this->addError(__('Common Utilities 2.3 is required in order to Advanced Forms works properly!', 'advform-pro'));
            return false;
        }

        return true;
    }

    public function on_uninstall()
    {
        return true;
    }

    public function on_update()
    {
        return true;
    }

    public function on_activate($q)
    {
        return true;
    }

    public function options()
    {

        return null;

    }

    /**
     * Demo of elements
     */
    public function main()
    {

        require 'includes/main-demo.php';

    }

    public function renderLocalField($field){
        $ele = null;

        switch ($field->field){
            case 'image-url':
                $ele = new RMFormImageUrl( (array) $field );
                break;
            case 'icon-selector':
                $ele = new AdvancedIconsField((array) $field);
                break;
            case 'webfonts':
                $ele = new RMFormWebfonts((array) $field);
                break;
            case 'color':
                $ele = new RMFormColorSelector((array) $field);
                break;
            case 'images-select':
                $ele = new RMFormImageSelect((array) $field);
                break;
            case 'countries':
                $ele = new AdvancedCountriesField((array) $field);
                break;
            case 'states':
                $ele = new AdvancedStatesField((array) $field);
                break;
            case 'repeater':
                $ele = new AdvancedRepeaterField((array) $field);
                break;
        }

        return $ele;
    }

    public function renderField($field){
        $ele = null;

        $valid = [
            'textbox' => 'RMFormText',
            'textarea' => 'RMFormTextArea',
            'select' => 'RMFormSelect',
            'yesno' => 'RMFormYesNo',
            'checkbox' => 'RMFormCheck',
            'radio' => 'RMFormRadio',
            'groups' => 'RMFormGroups',
            'editor' => 'RMFormEditor',
            'theme' => 'RMFormTheme',
            'lang' => 'RMFormLanguageField',
            'modules' => 'RMFormModules',
        ];

        $controls = [
            'text','select','textarea','email','file','datetime'
        ];

        if(array_key_exists($field->field, $valid)){

            $class = $valid[$field->field];
            $ele = new $class((array) $field);

        } else {
            $ele = $this->renderLocalField($field);
        }

        if(null == $ele){
            $ele = new RMFormText((array) $field);
        }

        if($ele->has('type') && in_array($ele->get('type'), $controls)){
            $ele->add('class', 'form-control');
        }

        return $ele;
    }

    static function getInstance(){
        static $instance;

        if(!isset($instance)){
            $instance = new AdvformproCUPlugin();
        }

        return $instance;
    }

}

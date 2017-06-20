<?php
/**
 * Advanced Forms Pro for Common Utilities
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Plugins\AdvForm;

class AdvancedDatetime extends \RMFormElement
{
    private $range = true;
    private $time = false;

    public function __construct($options = [])
    {
        global $common;

        if ( false == array_key_exists('parameters', $options) ){
            throw new \RMException(__('You must provide JavaScript parameters for datetime field', 'advform-pro'));
        }

        if(array_key_exists('norange', $options['parameters'])){
            $this->range = false;
        } else {
            $this->range = true;
            $options['parameters']['end'] = '#' . $this->get('id') . '-end';
        }

        if(array_key_exists('timepicker', $options['parameters'])){
            $this->time = true;
        }

        if(false == array_key_exists('lang', $options['parameters'])){
            $options['parameters']['lang'] = $common->settings->lang;
        }

        if(false == array_key_exists('formatDateTime', $options['parameters'])){
            $options['parameters']['formatDateTime'] = __('YYYY-MM-DD HH:mm:ss', 'advform-pro');
        }

        parent::__construct($options);

        if(false == $this->has('name')){
            throw new \RMException(__('You must provide a name for AdvancedDatetime field', 'advform-rpo'));
        }

        //$this->setIfNotSet('class', 'form-control');
        $this->set('data-advf-field', 'uploader');

        $this->suppressList = ['caption', 'params'];

    }

    public function render()
    {
        global $common;

        $common->template()->add_script('jquery.periodpicker.full.min.js', 'rmcommon', ['footer' => 1, 'id' => 'advf-datetime-js', 'directory' => 'plugins/advform-pro']);

        if($this->time){
            $common->template()->add_script('jquery.timepicker.min.js', 'rmcommon', ['footer' => 1, 'id' => 'advf-time-js', 'directory' => 'plugins/advform-pro']);
        }

        $common->template()->assign('parameters', $this->get('parameters'));
        $common->template()->assign('fieldID', $this->get('id'));
        $script = $common->template()->render('date-time.php', 'plugin', 'rmcommon', 'advform-pro');
        $common->template()->add_inline_script($script, 1);

        if($this->range){

            return '<input type="text" name="' . $this->get('name') . '[start]" id="'. $this->get('id') .'" value="' . $this->get('start') . '">
                    <input type="text" name="' . $this->get('name') . '[end]" id="'. $this->get('id') .'-end" value="' . $this->get('end') . '">';
        } else {

            return '<input type="text" name="' . $this->get('name') . '" id="'. $this->get('id') .'" value="' . $this->get('value') . '">';
        }

    }
}
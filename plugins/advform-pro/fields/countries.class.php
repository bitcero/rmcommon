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

class AdvancedCountriesField extends RMFormElement
{
    /**
     * AdvancedCountriesField constructor.
     * <pre>
     * $attributes = [
     *   'caption' => 'Caption',
     *   'name' => 'my_select',
     *   'id' => 'my-select',
     *   'multiple' => null
     * ]
     * </pre>
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $this->setIfNotSet('name', 'name_error');
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));

        $this->suppressList[] = 'selected';
        $this->suppressList[] = 'noscript';
    }

    public function render()
    {
        $selected = $this->get('selected');
        $attributes = $this->renderAttributeString();

        // Add script
        RMTemplate::getInstance()->add_script('chosen.min.js', 'rmcommon', [
            'id' => 'chosen-js',
            'directory' => 'plugins/advform-pro',
            'footer' => 1
        ]);

        if(!$this->has('noscript')){
            RMTemplate::getInstance()->add_inline_script('(function($){$("#'.$this->get('id').'").chosen();})(jQuery);', 1);
        }

        // Add styles
        RMTemplate::getInstance()->add_style('advforms.min.css', 'rmcommon', [
            'id' => 'adv-forms-css',
            'directory' => 'plugins/advform-pro'
        ]);

        $countries = include(RMCPATH . '/plugins/advform-pro/includes/countries.php');
        $selected = !is_array($selected) ? array() : $selected;

        $field = '<select ' . $attributes . ' data-advf-field="adv-countries">';
        $options = $this->get('options');
        foreach($countries as $code => $name){
            $field .= "\n<option value=\"$code\"". (in_array($code, $selected) ? ' selected' : '') .">$name</option>";
        }
        $field .= '</select>';

        return $field;
    }
}
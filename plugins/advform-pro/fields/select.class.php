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
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */
class AdvancedSelectField extends RMFormElement
{
    /**
     * AdvancedSelectField constructor.
     * <pre>
     * $attributes = [
     *   'caption' => 'Caption',
     *   'name' => 'my_select',
     *   'id' => 'my-select',
     *   'multi' => null,
     *   'options' => [
     *      'caption' => 'value',
     *      ...
     *   ]
     * ]
     * </pre>
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $this->setIfNotSet('name', 'name_error');
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));

        $this->suppressList[] = 'options';
        $this->suppressList[] = 'selected';
    }

    public function render()
    {
        $attributes = $this->renderAttributeString();

        // Add script
        RMTemplate::getInstance()->add_script('chosen.min.js', 'rmcommon', [
            'id' => 'chosen-js',
            'directory' => 'plugins/advform-pro',
            'footer' => 1
        ]);

        RMTemplate::getInstance()->add_inline_script('$("#'.$this->get('id').'").chosen();', 1);

        // Add styles
        RMTemplate::getInstance()->add_style('advforms.min.css', 'rmcommon', [
            'id' => 'adv-forms-css',
            'directory' => 'plugins/advform-pro'
        ]);

        $field = '<select ' . $attributes . '>';
        $options = $this->get('options');
        $selected = $this->get('selected');

        foreach($options as $caption => $value){
            $field .= "\n<option value=\"$value\"";
            $field .= in_array($value, $selected) ? ' selected' : '';
            $field .= ">$caption</option>";
        }

        $field .= '</select>';

        return $field;
    }
}
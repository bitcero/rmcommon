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
class AdvancedIconsField extends RMFormElement
{
    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $this->setIfNotSet('name', 'name_error');
        $this->setIfNotSet('value', '');
        $this->setIfNotSet('placeholder', __('Select an icon...', 'advform-pro'));
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));

        $this->add('class', 'adv-icons-selector');
        $this->add('class', 'form-control');
        $this->suppressList[] = 'name';
        $this->suppressList[] = 'value';
        $this->suppressList[] = 'required';
        $this->suppressList[] = 'placeholder';
        //$this->suppressList[] = 'placeholder';
    }

    public function render()
    {
        global $cuIcons;

        $attributes = $this->renderAttributeString();

        $placeholder = $this->has('placeholder') ? $this->get('placeholder') : __('Select an icon...', 'advform-pro');

        $field = '<div ' . $attributes . '>';

        if('' != $this->get('value')){
            $field .= '<div class="icons-icon">' . $cuIcons->getIcon($this->get('value')) . '</div>';
            $field .= ' <input type="text" name="' . $this->get('name') . '" value="' . $this->get('value') . '" class="icons-caption"';
            if( $this->has('required')){
                $field .= ' required';
            }
            $field .= ' placeholder="' . $placeholder . '">';
        } else {
            $field .= '<div class="icons-icon"></div>';

            $field .= ' <input type="text" name="' . $this->get('name') . '" class="icons-caption"';
            if( $this->has('required')){
                $field .= ' required';
            }
            $field .= ' value="' . $this->get('value') . '" placeholder="' . $placeholder . '">';
        }

        $field .= '<button type="button" class="clear-button">' . $cuIcons->getIcon('svg-rmcommon-trash') . '</button>';
        $field .= '<button type="button" class="icons-button">' . $cuIcons->getIcon('svg-rmcommon-search') . '</button>';

        $field .= '</div>';

        return $field;
    }
}
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
class AdvancedRepeaterField extends RMFormElement
{
    private $fields = [];

    public function __construct($attributes)
    {
        parent::__construct($attributes);

        $this->setIfNotSet('name', 'name_error');
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));
        $this->setIfNotSet('layout', 'vertical');
        $this->setIfNotSet('value', []);

        $this->add('class', 'adv-repeater-field');

        if ($this->has('layout')) {
            if ($this->get('layout') == 'horizontal') {
                $this->add('class', 'layout-horizontal');
            } else {
                $this->add('class', 'layout-vertical');
            }
        } else {
            $this->add('class', 'layout-vertical');
        }

        if ($this->has('fields')) {
            $this->fields = $this->get('fields');
        }

        $this->add('data-adv-field', 'repeater');
        $this->suppressList[] = 'name';
        $this->suppressList[] = 'value';
        $this->suppressList[] = 'legend';
        $this->suppressList[] = 'fields';
        $this->suppressList[] = 'layout';
    }

    /**
     * Add a new field to repeater
     * @param array $field
     * @return bool
     */
    public function addField($field)
    {
        if (!is_array($field)) {
            return false;
        }

        $this->fields[] = $field;
    }

    public function render()
    {
        global $cuIcons;

        $attributes = $this->renderAttributeString();
        $plugin = \Common\Core\Helpers\Plugins::getInstance()->load('advform-pro');

        $repeat = '<div ' . $attributes . ' data-name="' . $this->get('name') . '">';
        $repeat .= '<div class="hidden-fields" data-repeater="repeater">';

        $fields = $this->fields;
        foreach ($fields as $data) {
            $data = (object)$data;
            $field = $plugin->renderField($data);
            // Change name
            $field->set('name', 'x-repeat[repeat-num][' . $data->name . ']');
            // Change id
            $field->set('id', 'x-repeat-' . (isset($data->id) ? $data->id : $data->name) . '-repeat-num');

            // Countries
            if ($data->field == 'countries') {
                $field->set('noscript', null);
                $field->set('data-type', 'countries-repeat-num');
            }

            if ($data->field == 'states') {
                $field->set('noscript', null);
                $field->set('data-type', 'states-repeat-num');
                if ($field->has('data-country-field')) {
                    $field->set('data-country-field', 'repeater-' . $field->get('data-country-field') . '-repeat-num');
                };
            }

            if ($data->field == 'color') {
                $field->set('data-type', 'color-repeat-num');
            }

            $repeat .= '<div data-repeater="item"' . ($this->get('layout') == 'horizontal' ? ' data-size="' . $data->fieldsize . '"' : '') . '>' .
                '<label>' . $field->get('caption') . '</label>' .
                $field->render();

            if (isset($data->description)) {
                $repeat .= '<small class="help-block">' . $data->description . '</small>';
            }

            $repeat .= '</div>';
        }

        $repeat .= '</div>';

        // Visible Container
        $repeat .= '<div class="repeater-fields" data-repeater="container">';

        $values = $this->get('value');
        foreach ($values as $id => $value) {
            $repeat .= '<div data-repeater="row" class="repeater-row collapsed" data-id="' . $id . '">';
            foreach ($fields as $data) {
                $data = (object)$data;

                $data->value = isset($value[$data->name]) ? $value[$data->name] : '';

                $field = $plugin->renderField($data);
                $field->set('name', $this->get('name') . '[' . $id . '][' . $data->name . ']');
                $field->set('id', $this->get('name') . '-' . (isset($data->id) ? $data->id : $data->name) . '-' . $id);
                $repeat .= '<div data-repeater="field" class="repeater-field"' . ($this->get('layout') == 'horizontal' ? ' style="width:' . $data->fieldsize . '%"' : '') . '>' .
                    '<label>' . $field->get('caption') . '</label>' .
                    $field->render();

                if (isset($data->description)) {
                    $repeat .= '<small class="help-block">' . $data->description . '</small>';
                }

                $repeat .= '</div>';
            }

            $placeholder = $this->has('data-placeholder') ? $this->get('data-placeholder') : __('Element %u', 'advform-pro');

            $repeat .= '<div class="repeater-field-controls ' . $this->get('data-color') . '">' .
                '<span class="caption">' . sprintf($placeholder, $id + 1) . '</span>' .
                '<button class="open" type="button" title="' . __('Open', 'advform-pro') . '">' . $cuIcons->getIcon('svg-rmcommon-double-arrow-up') . '</button>' .
                '<button class="delete" type="button" title="' . __('Delete', 'advform-pro') . '">' . $cuIcons->getIcon('svg-rmcommon-trash') . '</button>' .
                '</div>';

            $repeat .= "</div>";
        }

        $repeat .= '</div>';

        //if(empty($this->get('values'))){
        $repeat .= '<div class="repeater-start-controls"><button type="button" data-repeater="button">' . $cuIcons->getIcon('svg-rmcommon-plus') . ' ' . __('Add Row', 'advform-pro') . '</button></div>';
        //}

        $repeat .= '</div>';

        return $repeat;
    }
}
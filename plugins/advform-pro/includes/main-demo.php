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
 * @package      
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

define('RMCLOCATION', 'advform-demo');

RMTemplate::get()->header();

echo '<h1 class="cu-section-title">' . __('Advanced Forms Demo', 'advform-pro') . '</h1>';

$form = new RMForm('', '', '');
$form->fieldClass = '';

$color = new RMFormColorSelector(__('Pick a color:', 'advform-pro'), 'color', '#FF0000', true);
$form->addElement($color);

$image = new RMFormImageSelect(__('Pick an image:', 'advform-pro'), 'image', '');
$image->addImage('img_errors', XOOPS_URL . '/images/img_errors.png');
$image->addImage('password', XOOPS_URL . '/images/password.png');
$image->addImage('password', XOOPS_URL . '/images/poweredby.gif');
$form->addElement($image);

$imgurl = new RMFormImageUrl(__('Image URL:', 'advform-pro'), 'url', 'http://xoops.org/themes/wox/images/giftshop.jpg');
$form->addElement($imgurl);

$font = new RMFormWebfonts(__('Pick a font:', 'advform-pro'), 'font');
$form->addElement($font);

$slider = new RMFormSlider(__('Sliders Creator:', 'advform-pro'), 'slider');
$slider->addField('title', array(
    'caption' => __('Specify the title for this slider', 'inception'),
    'description' => '',
    'type' => 'textbox'
));
$slider->addField('content', array(
    'caption' => __('Text content', 'inception'),
    'description' => '',
    'type' => 'textarea'
));
$slider->addField('image', array(
    'caption' => __('Select image for slider', 'inception'),
    'description' => __('Description for this field.', 'xthemes'),
    'type' => 'imageurl'
));
$form->addElement($slider);

//--
$icon = new RMFormIconsPicker(__('All icons:', 'advform-pro'), 'icon', array(
    'selected' => 'fa fa-flag',
    'moon' => true,
    'fontawesome' => true,
    'glyphicons' => true,
    'svg' => true
));
$icon->setDescription(__('With FontAwesome and Glyphicons active', 'advform-pro'));
$form->addElement($icon);

//--
$icon = new RMFormIconsPicker(__('Glyphicons Icons:', 'advform-pro'), 'icon1', array(
    'selected' => 'glyphicon glyphicon-plus',
    'glyphicons' => true,
    'fontawesome' => false,
    'svg' => false
));
$icon->setDescription(__('Only Glyphicons active', 'advform-pro'));
$form->addElement($icon);

//--
$icon = new RMFormIconsPicker(__('FontAwesome Icons:', 'advform-pro'), 'icon2', array(
    'selected' => 'fa fa-flag',
    'glyphicons' => false,
    'svg' => false
));
$icon->setDescription(__('Only FontAwesome active', 'advform-pro'));
$form->addElement($icon);

//--
$icon = new RMFormIconsPicker(__('SVG Icons:', 'advform-pro'), 'icon3', array(
    'selected' => 'svg-rmcommon-xoops',
    'glyphicons' => false,
    'fontawesome' => false
));
$icon->setDescription(__('Only FontAwesome active', 'advform-pro'));
$form->addElement($icon);

$icon = new AdvancedIconsField([
    'name' => 'icons',
    'id' => 'icons-selector',
    'caption' => __('Icon Selector:', 'advform-pro'),
    'placeholder' => __('Select an icon...', 'advform-pro')
]);
$form->addElement($icon);

$repeater = new AdvancedRepeaterField([
    'name' => 'repeater',
    'id' => 'repeater-id',
    'caption' => __('Repeater field', 'advform-pro'),
    'layout' => 'vertical',
    'data-placeholder' => __('Repeater item %u', 'advform-pro'),
    'data-color' => 'default',
    'value' => [
        [
            'icon' => 'svg-rmcommon-rmcommon',
            'image' => 'http://xoops.redmexico.com.mx/uploads/2015/08/sizes/logo-redmexico-thumbnail.png',
            'webfonts' => '',
            'color' => '#F6F444',
        ],
        [
            'icon' => 'svg-shopper-store',
            'image' => 'http://xoops.redmexico.com.mx/uploads/2015/07/sizes/of02-thumbnail.jpg',
            'webfonts' => '',
            'color' => '#FFCC00'
        ]
    ]
]);
$repeater->addField([
    'name' => 'icon',
    'caption' => __('Icon Selector:', 'advform-pro'),
    'field' => 'icon-selector',
    'id' => 'icon',
    'fieldsize' => '50',
    'placeholder' => __('Select an icon...', 'advform-pro')
]);
$repeater->addField([
    'name' => 'image',
    'value' => '',
    'caption' => __('Image URL:', 'advform-pro'),
    'field' => 'image-url',
    'id' => 'image'
]);
$repeater->addField([
    'name' => 'webfonts',
    'caption' => __('Pick a font:', 'advform-pro'),
    'field' => 'webfonts',
    'id' => 'webfonts',
    'fieldsize' => '50'
]);
$repeater->addField([
    'name' => 'color',
    'caption' => __('Pick a color:', 'advform-pro'),
    'field' => 'color',
    'id' => 'webfonts',
    'initial' => '#FF0000',
    'hash' => true
]);
$repeater->addField([
    'name' => 'country',
    'caption' => __('Select a country:', 'advform-pro'),
    'field' => 'countries',
    'id' => 'country',
    'initial' => 'MX',
    'multiple' => null
]);
$repeater->addField([
    'name' => 'state',
    'caption' => __('Select a state:', 'advform-pro'),
    'field' => 'states',
    'id' => 'state',
    'multiple' => null,
    'data-country' => 'MX|US',
    'data-country-field' => 'country',
]);

$form->addElement($repeater);

$select = new AdvancedSelectField([
    'caption' => __('Advanced select with single selection', 'advform-pro'),
    'name' => 'test',
    'id' => 'test-field',
    'class' => 'form-control',
    'title' => 'Advanced Select',
    'options' => [
        'Hola' => 'hola',
        'Como' => 'como',
        'Estás' => 'estas',
        'hello' => 'Hello',
        'how' => 'How',
        'are' => 'Are',
        'you' => 'you'
    ]
]);
$form->addElement($select);

$select2 = new AdvancedSelectField([
    'caption' => __('Advanced select with multiple selection', 'advform-pro'),
    'name' => 'test1',
    'id' => 'test-field2',
    'class' => 'form-control',
    'title' => 'Multiple selection',
    'data-no-results' => 'No results for ',
    'multiple' => null,
    'options' => [
        'Hola' => 'hola',
        'Como' => 'como',
        'Estás' => 'estas',
        'hello' => 'Hello',
        'how' => 'How',
        'are' => 'Are',
        'you' => 'you'
    ]
]);
$form->addElement($select2);

$countries = new AdvancedCountriesField([
    'caption' => __('Advanced countries selection', 'advform-pro'),
    'name' => 'countries',
    'id' => 'test-countries',
    'class' => 'form-control',
    'title' => 'Multiple selection',
    'data-no-results' => 'No results for ',
    'multiple' => null,
    'selected' => ['MX']
]);
$form->addElement($countries);

$countries = new AdvancedStatesField([
    'caption' => __('Advanced states selection', 'advform-pro'),
    'name' => 'states',
    'id' => 'test-states',
    'class' => 'form-control',
    'title' => 'States with multiple selection',
    'data-no-results' => 'No results for ',
    'multiple' => null,
    'data-country' => 'MX',
    'data-country-field' => 'test-countries',
    'value' => ['YUC', 'AGU']
]);
$form->addElement($countries);

$form->addElement(new RMFormButton([
    'caption' => 'Reset',
    'type' => 'reset'
]));

$path = RMCPATH . '/plugins/advform-pro/includes/';

$form->display();

RMTemplate::get()->footer();
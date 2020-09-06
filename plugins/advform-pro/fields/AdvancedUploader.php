<?php
/**
 * Advanced Forms Pro for Common Utilities
 *
 * Copyright © 2015 - 2017 Eduardo Cortés http://www.eduardocortes.mx
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

class AdvancedUploader extends \RMFormElement
{
    private $parameters = [];

    public function __construct($options)
    {
        if ( false == array_key_exists('parameters', $options) ){
            throw new \RMException(__('You must provide JavaScript parameters for uploader field', 'advform-pro'));
        }

        parent::__construct($options);

        $this->parameters = $options['parameters'];
        if(false == array_key_exists('dictDefaultMessage')){
            $this->parameters['dictDefaultMessage'] = __('Drop files here to upload', 'adform-pro');
        }

        $this->setIfNotSet('id', $this->get('name') . '-uploader');
        $this->set('data-advf-field', 'uploader');

        $this->suppressList = ['caption', 'params'];
    }

    public function render()
    {
        global $common;

        $common->template()->add_script('dropzone.min.js', 'rmcommon', ['id' => 'dropzone-js', 'footer' => 1]);

        $attributes = $this->renderAttributeString();

        // Render javascript initializer
        $common->template()->assign('parameters', $this->parameters);
        $common->template()->assign('id', $this->get('id'));
        $common->template()->assign('name', $this->get('name'));
        $common->template()->assign('attributes', $attributes);

        $html = $common->template()->render('uploader-html.php', 'plugin', 'rmcommon','advform-pro');
        $script = $common->template()->render('uploader.php', 'plugin', 'rmcommon','advform-pro');
        $common->template()->add_inline_script($script, 1);

        return $html;
    }
}
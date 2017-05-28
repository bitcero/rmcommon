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
    public function __construct($options)
    {

        if(array_key_exists('url', $options)){
            $options['data-url'] = $options['url'];
        }

        if(array_key_exists('files', $options)){
            $options['data-files'] = $options['files'];
        }

        parent::__construct($options);

        $this->setIfNotSet('id', $this->get('name') . '-uploader');
        $this->set('data-advf-field', 'uploader');

        $this->suppressList = ['caption'];
    }

    public function render()
    {
        global $common;

        $common->template()->add_script('dropzone.min.js', 'rmcommon', ['id' => 'dropzone-js', 'footer' => 1]);

        $attributes = $this->renderAttributeString();

        $html = '<div '. $attributes .'><form class="dropzone"></form></div>';
        return $html;
    }
}
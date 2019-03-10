<?php
/**
 * Common Utilities for XOOPS
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

namespace Common\API\Editors\Quill;

use Common\API\Editors\Editor;

class Quill implements Editor
{
    private $options = [];
    private $name = '';

    public function __construct($name)
    {
        global $common;

        $this->name = \TextCleaner::sweetstring($name);

        $this->options = [
            'theme' => 'snow',
            'debug' => 'info',
            'modules' => [
                'syntax' => true,
                'toolbar' => '#' . $this->name . '-toolbar',
            ],
            'placeholder' => '',
            'readOnly' => false,
        ];
    }

    /**
     * Visit {@link https://quilljs.com/docs/configuration/} Configuración de Quill
     * @param string|array $name Name of the option key or an array with all name => value
     * @param mixed $value
     */
    public function setOptions($name, $value)
    {
        if (is_array($name)) {
            $this->options = array_merge($this->options, $name);
        } else {
            $this->options[$name] = $value;
        }
    }

    public function getOptions($name = null)
    {
        if (null === $name) {
            return $this->options;
        }

        return $this->options[$name];
    }

    public function render($template = '')
    {
        global $common;

        // Add js to HTML page
        $common->template()->add_script(
            'quill.min.js',
            'rmcommon',
            [
                'footer' => 1,
                'required' => 'jquery',
                'directory' => 'api/editors/Quill',
                'id' => 'quill-js',
            ]
        );

        $theme = $this->options['theme'];
        if ('' == $theme) {
            $theme = 'snow';
        }

        // Add CSS to HTMl page
        $common->template()->add_style(
            'quill.' . $theme . '.css',
            'rmcommon',
            [
                'directory' => 'api/editors/Quill',
                'id' => 'quill-css',
            ]
        );

        if ('' == $template || false === file_exists($template)) {
            $template = $common->template()->path('api/quill.php', 'module', 'rmcommon');
        }

        $common->template()->add_inline_script($this->js(), 1);

        $common->template()->assign('name', $this->name);
        $html = $common->template()->render($template);

        return $html;
    }

    public function js()
    {
        $js = 'var ' . preg_replace('/[^a-zA-Z]/', '', $this->name) . "Editor = new Quill('#" . $this->name . "', " . json_encode($this->options) . ');';

        return $js;
    }
}

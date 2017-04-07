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

namespace Common\API\Editors;

interface Editor
{
    /**
     * Editors must accept configuration options for
     * their own parameters (generally in JS). $name parameter
     * can be an array with name => value option.
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setOptions($name, $value);

    /**
     * Returns all confgiured options (when $name is null) for a specific editor or
     * returns a specific options when $name is prodived.
     * @param null $name
     * @return mixed
     */
    public function getOptions($name = null);

    /**
     * Generated all HTML code for editor
     * @param string $template
     * @return string
     */
    public function render($template = '');

    /**
     * Returns the javascript code for editor when applies.
     * @return string
     */
    public function js();
}
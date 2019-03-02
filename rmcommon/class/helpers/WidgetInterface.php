<?php
/**
 * Common Utilities framework for Xoops
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

/**
 * Interface WidgetInterface
 *
 * All widgets must implement this interface
 *
 * @package Common\Core\Helpers
 */
interface WidgetInterface
{

    /**
     * Returns internal id for widget
     * @return mixed
     */
    public function id();

    /**
     * Initialize the widget
     * @param mixed $parameters
     */
    public function setup($parameters);

    /**
     * Returns the template used for this widget
     * @return mixed
     */
    public function template();

    /**
     * Returns the HTML code generated for widget
     * @return mixed
     */
    public function getHtml();
}

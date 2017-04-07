<?php
/**
 * Common Utilities Framework for Xoops
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

class Comments
{

    /**
     * Get all comments for given parameters
     *
     * Example:
     * <pre>
     * $parameters = [
     *    'url' => 'the URL of your page',
     *    'object' => 'mywords',
     *    'type' => 'module',
     *    'identifier' => 'post=234',
     *    'parent' => 0,
     *    'user' => null,
     *    'assign' => true
     * ];</pre>
     *
     * Explanation:
     *
     * You can use the parameters variable to pass all parameters needed to the
     * comments service (rmcommon or plugin) in order to get your comments working, however
     * 'url' and 'identifier' are parameters highly recommended due that both will
     * prevent to duplicate comments storage.
     *
     * @param array $parameters All parameters to use the comments
     * @return mixed
     */
    public function load($parameters)
    {
        global $common;

        if ($common->services()->service('comments')) {

            return $common->services()->comments->load($parameters);

        }

        return null;
    }

    /**
     * Create the comments form
     * You need to include the template 'rmc-comments-form.html' where
     * you wish to show this form
     *
     * Example:
     * <pre>
     * $parameters = [
     *    'url' => 'the URL of your page',
     *    'object' => 'mywords',
     *    'type' => 'module',
     *    'identifier' => 'post=234',
     *    'file' => 'path to your controller file',
     * ];
     * </pre>
     *
     * The 'file' parameter must indicate the path for a controller file to update
     * comments in module or element.
     *
     * @param array $parameters
     * return mixed
     *
     */
    public function form($parameters)
    {
        global $common;

        if($common->services()->service('comments')){
            return $common->services()->comments->form($parameters);
        }
    }

    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new Comments();
        }

        return $instance;
    }
}
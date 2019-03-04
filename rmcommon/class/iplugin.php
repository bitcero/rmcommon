<?php
/**
 * Common Utilities Framework for XOOPS
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

/**
 * Interface for Common Utilities plugins
 */
abstract class RMIPlugin
{
    protected $info = [];
    protected $settings = [];
    protected $errors = [];

    public function on_install()
    {
        return true;
    }

    public function on_uninstall()
    {
        return true;
    }

    public function on_update()
    {
        return true;
    }

    public function on_activate($q)
    {
        return true;
    }

    public function options()
    {
        return [];
    }

    public function get_info($name)
    {
        if (!isset($this->info[$name])) {
            return '';
        }

        return $this->info[$name];
    }

    public function info()
    {
        return $this->info;
    }

    public function settings($name = '')
    {
        $settings = empty($this->settings) ? RMFunctions::get()->plugin_settings($this->get_info('dir'), true) : $this->settings;

        if (isset($settings[$name])) {
            return $settings[$name];
        }

        return $settings;
    }

    public function addError($error_string)
    {
        $this->errors[] = $error_string;
    }

    public function errors($lines = true)
    {
        if ($lines) {
            return implode('<br>', $this->errors);
        }

        return $this->errors;
    }

    public function path()
    {
        return RMCPATH . '/plugins/' . $this->get_info('dir');
    }

    public function url()
    {
        return RMCURL . '/plugins/' . $this->get_info('dir');
    }

    public function isActive()
    {
        return \Common\Core\Helpers\Plugins::isInstalled($this->info['dir']);
    }

    abstract public static function getInstance();
}

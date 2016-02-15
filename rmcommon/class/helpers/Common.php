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

use RMBreadCrumb;
use RMSettings;
use RMTemplate;
use RMHttpRequest;
use RMCustomCode;
use RMEvents;
use RMUtilities;
use RMFormat;
use RMTimeFormatter;
use RMPrivileges;
use RMUris;
use Common\Core\Helpers\Icons;
use RMModules;
use Common\Core\Helpers\Services;

class Common
{
    /**
     * @return RMBreadCrumb
     */
    public function breadcrumb()
    {
        return RMBreadCrumb::get();
    }

    /**
     * @return RMSettings
     */
    public function moduleSettings()
    {
        static $settingsInstance;

        if (!isset($settingsInstance)) {
            $settingsInstance = new RMSettings();
        }

        return $settingsInstance;
    }

    /**
     * @return RMTemplate
     */
    public function template()
    {
        return RMTemplate::getInstance();
    }

    /**
     * @return RMHttpRequest
     */
    public function httpRequest()
    {
        static $httpInstance;

        if (!isset($httpInstance)) {
            $httpInstance = new RMHttpRequest();
        }

        return $httpInstance;
    }

    /**
     * @return RMCustomCode
     */
    public function customCode()
    {
        return RMCustomCode::get();
    }

    /**
     * @return RMEvents
     */
    public function events()
    {
        return RMEvents::get();
    }

    /**
     * @return RMUtilities
     */
    public function utilities()
    {
        return RMUtilities::get();
    }

    /**
     * @return RMFormat
     */
    public function format()
    {
        static $formatInstance;

        if (!isset($formatInstance)) {
            $formatInstance = new RMFormat();
        }

        return $formatInstance;
    }

    /**
     * @param string $format
     * @return RMTimeFormatter
     */
    public function timeFormat($format = '')
    {
        return RMTimeFormatter::get($format);
    }

    /**
     * @return RMPrivileges
     */
    public function privileges()
    {
        return RMPrivileges::getInstance();
    }

    /**
     * @return RMUris
     */
    public function uris()
    {
        static $urisInstance;

        if (!isset($urisInstance)) {
            $urisInstance = new RMUris();
        }

        return $urisInstance;
    }

    /**
     * @return \Common\Core\Helpers\Icons
     */
    public function icons()
    {
        return Icons::getInstance();
    }

    /**
     * @return RMModules
     */
    public function modules()
    {
        static $modInstance;

        if (!isset($modInstance)) {
            $modInstance = new RMModules();
        }

        return $modInstance;
    }

    /**
     * @return \Common\Core\Helpers\Services
     */
    public function services()
    {
        return Services::getInstance();
    }

    /**
     * Get the path for a Common Utilities file (inside module folder)
     * @param string $path
     * @return string
     */
    public function path($path = '')
    {

        $base = XOOPS_ROOT_PATH . '/modules/rmcommon';

        if ('' == trim($path)) {
            return $base;
        }

        return $base . '/' . ltrim($path, '/');

    }

    /**
     * Get URL for a Common Utilities file (inside module folder)
     * @param string $path
     * @return mixed
     */
    public function url($path = '')
    {
        $path = $this->path($path);
        return str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $path);
    }

    /**
     * AJAX Helper
     */
    public function ajax(){
        return \Rmcommon_Ajax::getInstance();
    }

    /**
     * @return Common
     */
    public static function getInstance(){
        static $instance;

        if (!isset($instance)) {
            $instance = new Common();
        }

        return $instance;
    }

}
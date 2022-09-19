<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
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
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

use RMBreadCrumb;
use RMCustomCode;
use RMEvents;
use RMFormat;
use RMHttpRequest;
use RMModules;
use RMPrivileges;
use RMSettings;
use RMTemplate;
use RMTimeFormatter;
use RMUris;
use RMUtilities;

class Common
{
    public $isAjax = false;
    private $helps = [];

    /**
     * Determines if current theme is a native theme (xThemes)
     * or a standard xoops theme
     * @var bool
     */
    public $nativeTheme = false;
    /**
     * Common Utilities settings
     * @var
     */
    public $settings;

    public $location = '';

    public function __construct()
    {
        global $cuSettings;
        $this->settings = $cuSettings;
        $this->location = new \stdClass();
    }

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
    public function settings()
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
        $formater = RMTimeFormatter::get($format);
        if ('' != $format) {
            $formater->format = $format;
        }

        return $formater;
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
    public function ajax()
    {
        $this->isAjax = true;

        return \Rmcommon_Ajax::getInstance();
    }

    public function security()
    {
        global $xoopsSecurity;

        return $xoopsSecurity;
    }

    /**
     * @return \XoopsMySQLDatabase
     */
    public function db()
    {
        global $xoopsDB;

        return $xoopsDB;
    }

    /**
     * @return App
     */
    public function app()
    {
        $app = App::getInstance();

        return $app;
    }

    /**
     * Check the session token sent via HTTP and redirect if not valid
     * @param bool $ajax
     * @param string $url Used only when $ajax is false
     * @return bool
     */
    public function checkToken($ajax = true, $url = '')
    {
        if ($this->security()->check(true, false, 'CUTOKEN')) {
            return true;
        }

        if ($ajax) {
            $this->ajax()->response(
                __('Session token expired!', 'rmcommon'),
                1,
                0,
                ['reload' => true]
            );
        } else {
            $this->uris()->redirect_with_message(
                __('Session token expired!', 'rmcommon'),
                '' == $url ? XOOPS_URL : $url,
                RMMSG_ERROR
            );
        }
    }

    /**
     * @return \RMImageResizer
     */
    public function resize()
    {
        $resizer = \RMImageResizer::getInstance();

        return $resizer;
    }

    /**
     * Comments handler
     * @return Comments
     */
    public function comments()
    {
        $comments = Comments::getInstance();

        return $comments;
    }

    public function plugins()
    {
        return Plugins::getInstance();
    }

    // Widgets handler
    public function widgets()
    {
        $widgets = Widgets::getInstance();

        return $widgets;
    }

    /**
     * @return Common
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public function xoopsTpl()
    {
        global $xoopsTpl;

        return $xoopsTpl;
    }

    public function help($directory)
    {
        if (false === array_key_exists($directory, $this->helps)) {
            $this->helps[$directory] = new Help($directory);
        }

        return $this->helps[$directory];
    }

    public function crypt($method = null, $key = null)
    {
        $crypt = new \Crypt($method, $key);

        return $crypt;
    }
}

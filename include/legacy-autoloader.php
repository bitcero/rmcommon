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
 * @param mixed $class
 */

/**
 * This file contains the autoloader function files from RMCommon Utilities
 */
function rmc_autoloader($class)
{
    global $xoopsModule;

    if (class_exists($class)) {
        return;
    }

    /**
     * New autoloader method
     * $class = new Module_ClassName();
     * The class name must contain the module directory name separated with a "_"
     * from the file name.
     * Common Utilities will search for "PATH/module/classname.class.php" file
     */
    $data = explode('_', mb_strtolower($class));

    if (count($data) >= 2) {
        if ('editor' == $data[0]) {
            $file = RMCPATH . '/api/editors/' . $data[1] . '/' . mb_strtolower($data[1]) . '.php';
            if (file_exists($file)) {
                require $file;

                return null;
            }
        } elseif (is_dir(XOOPS_ROOT_PATH . '/modules/' . $data[0])) {
            // Module exists! Then will search for /{dir}/{class}.class.php
            $name = mb_substr(mb_strtolower($class), mb_strlen($data[0]) + 1);
            $file = XOOPS_ROOT_PATH . '/modules/' . $data[0] . '/class/' . mb_strtolower(str_replace('_', '-', $name)) . '.class.php';
            if (is_file($file)) {
                require $file;

                return;
            }

            // Helpers from rmcommon have a different name structure
            if ('rmcommon' == $data[0]) {
                $file = XOOPS_ROOT_PATH . '/modules/rmcommon/class/helpers/' . mb_strtolower(str_replace('_', '.', $class)) . '.class.php';
                if (is_file($file)) {
                    require $file;

                    return;
                }
            }
        }
    }

    /**
     * Old method maintained for backward compatibility
     */
    $class = str_replace('\\', '/', $class);

    $class = mb_strtolower($class);

    if ('xoopskernel' == $class) {
        return;
    }

    if ('rm' == mb_substr($class, 0, 2)) {
        $class = mb_substr($class, 2);
    }

    if ('handler' == mb_substr($class, mb_strlen($class) - mb_strlen('handler'))) {
        $class = mb_substr($class, 0, mb_strlen($class) - 7);
    }

    $class = str_replace('_', '-', $class);

    $paths = [
        '/api',
        '/class',
        '/class/ar',
        '/class/helpers',
        '/class/modules',
        '/class/fields',
        '/class/form',
        '/kernel',
    ];

    if (is_a($xoopsModule, 'XoopsModule') && 'system' != $xoopsModule->dirname()) {
        $paths[] = '/modules/' . $xoopsModule->dirname() . '/class';
    }

    foreach ($paths as $path) {
        if (file_exists(RMCPATH . $path . '/' . $class . '.class.php')) {
            require_once RMCPATH . $path . '/' . $class . '.class.php';
            break;
        } elseif (file_exists(RMCPATH . $path . '/' . $class . '.php')) {
            require_once RMCPATH . $path . '/' . $class . '.php';
            break;
        } elseif (file_exists(RMCPATH . $path . '/' . $class . '.trait.php')) {
            require_once RMCPATH . $path . '/' . $class . '.trait.php';
            break;
        } elseif (file_exists(XOOPS_ROOT_PATH . $path . '/' . $class . '.php')) {
            require_once XOOPS_ROOT_PATH . $path . '/' . $class . '.php';
            break;
        } elseif (file_exists(XOOPS_ROOT_PATH . $path . '/' . $class . '.class.php')) {
            require_once XOOPS_ROOT_PATH . $path . '/' . $class . '.class.php';
            break;
        }
    }
}

spl_autoload_register('rmc_autoloader');

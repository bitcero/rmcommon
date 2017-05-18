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

/**
 * This file contains the class that allows to rmcommon work with URIS
 */
class RMUris
{

    /**
     * Encode array keys to make a valid url string
     *
     * @param array $array to encode
     * @param string $name name to generate url
     * @param string $param_separator A valid URL param separator (&)
     * @return string
     */
    static function url_encode_array($array, $name, $param_separator = '&')
    {

        return http_build_query($array, 'var_', $param_separator);

    }

    /**
     * Returns the current browser
     * @return string
     */
    static function current_url()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        return $pageURL;
    }


    static function anchor($module, $controller = '', $action = '', $parameters = array())
    {
        global $cuSettings;

        if ($module == '')
            return null;

        $url = XOOPS_URL;

        $paths = isset($cuSettings->modules_path) ? $cuSettings->modules_path : array();
        $path = isset($paths[$module]) ? $paths[$module] : '/' . $module;

        if (defined('XOOPS_CPFUNC_LOADED')) {

            if ($cuSettings->permalinks && isset($paths[$module])) {
                $url .= '/admin' . $path;
            } else {
                $objModule = XoopsModule::getByDirName($module);
                $url .= '/modules/' . $module . '/' . $objModule->getInfo('adminindex');
            }

        } else
            $url .= $cuSettings->permalinks ? $path : '/modules/' . $module;

        if ($controller == '')
            return $url;

        $url .= $cuSettings->permalinks ? '/' . $controller . '/' : '/' . $controller . '/';
        $url .= $action != '' ? $action . '/' : '';
        $query = '';

        foreach ($parameters as $name => $value) {
            $query .= $name . '/' . urlencode($value) . '/';
        }

        return $url . $query;

    }

    static function relative_anchor($module, $controller, $action = '', $parameters = array())
    {
        $url = self::anchor($module, $controller, $action, $parameters);

        $url = str_replace(XOOPS_URL, '', $url);

        return $url;

    }

    /**
     * Gets a relative URL to file/folder
     * @param string $path
     * @return mixed|string
     */
    static function relative_url($path = '')
    {

        $url = 'http';
        if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
            $url .= "s";
        }
        $url .= "://";
        $url .= $_SERVER["HTTP_HOST"];

        if (FALSE === strpos($path, XOOPS_URL))
            $path = XOOPS_URL . '/' . ltrim($path, "/");

        $url = str_replace($url, '', $path);

        /*if ( '' != $path )
            $url .= '/' . $path;*/

        return $url;

    }

    /**
     * Redirect the browser to a new URL
     * @param string $message Message to show
     * @param string $url New URL
     * @param int $level Warning level
     * @param string $icon Icon URL (optional)
     */
    static function redirect_with_message($message, $url, $level = RMMSG_WARN, $icon = '')
    {
        global $common;

        if($common->nativeTheme){
            $i = isset($_SESSION['cu_redirect_messages']) ? count($_SESSION['cu_redirect_messages']) + 1 : 0;
            $_SESSION['cu_redirect_messages'][$i]['text'] = htmlentities($message);
            $_SESSION['cu_redirect_messages'][$i]['level'] = $level;
            $_SESSION['cu_redirect_messages'][$i]['icon'] = $icon;
        } else {
            // legacy
            $_SESSION['redirect_message'] = $message;
        }

        if (!headers_sent()) {
            header('location: ' . preg_replace("/[&]amp;/i", '&', $url));
            die();
        }

        $ret = '<script>window.location.href = "' . $url . '";</script><noscript><meta http-equiv="refresh" content="0;url=' . $url . '"></noscript>';
        echo $ret;
        die();

    }

    /**
     * Crea la url para una imagen dada de acuerdo al módulo
     * @param string $module Directorio del módulo
     * @param string $image Nombre del archivo de la imagen
     * @return string URL completa de la imagen
     */
    static function image($module, $image)
    {

        if ($module == '')
            return false;

        $url = XOOPS_URL . '/modules/' . $module . '/images/' . $image;
        return $url;

    }

    /**
     * Crea la URL para un archivo cualquiera dentro de XOOPS
     * @param string $module The module directory
     * @param string $file Name of file inside module/directory
     * @param string $directory Directory inside module
     * @return string
     */
    static function file($module, $file, $directory = '')
    {

        if ($module == '' || $file == '')
            return '';

        $partial = trim($module, '/');
        $partial = trim($partial, '\\');

        if ($directory != '')
            $partial .= trim($directory, '/');

        $partial .= $file;

        if (!file_exists(XOOPS_ROOT_PATH . '/' . $partial))
            return '';

        return XOOPS_URL . '/' . $partial;

    }

    /**
     * Gets an URL for a Common Utilities page
     * @param string $page Page to link
     * @return string
     */
    static function system_url($page)
    {
        global $cuSettings;

        $url = XOOPS_URL;

        switch ($page) {

            case 'rss':
                $url .= $cuSettings->permalinks ? '/rss/' : '/backend.php';
                break;

            default:
                $url .= '/' . $page;
                break;

        }

        return $url;

    }

}

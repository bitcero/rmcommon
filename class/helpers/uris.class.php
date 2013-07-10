<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: http://www.redmexico.com.mx
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
    static function url_encode_array($array, $name, $param_separator='&'){

        return http_build_query($array, 'var_', $param_separator);

    }

    /**
     * Returns the current browser
     * @return string
     */
    static function current_url() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        $pageURL .= $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        return $pageURL;
    }


    static function parameter($method, $key, $type, $default = ''){




    }

    /**
     * Redirect the browser to a new URL
     * @param string $message Message to show
     * @param string $url New URL
     * @param int $level Warning level
     * @param string $icon Icon URL (optional)
     */
    static function redirect_with_message($message, $url, $level = RMMSG_WARN, $icon = ''){

        $i = isset($_SESSION['rmMsg']) ? count($_SESSION['rmMsg']) + 1 : 0;
        $_SESSION['rmMsg'][$i]['text'] = htmlentities($message);
        $_SESSION['rmMsg'][$i]['level'] = $level;
        $_SESSION['rmMsg'][$i]['icon'] = $icon;
        header('location: '.preg_replace("/[&]amp;/i", '&', $url));
        die();

    }

}
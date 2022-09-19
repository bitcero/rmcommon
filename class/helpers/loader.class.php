<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema Base
@author     Red México
@author     https://bitcero.dev
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/
class RMLoader
{
    public static function api($name, $class)
    {
        if (class_exists($class)) {
            return false;
        }

        $dir = RMCPATH . '/api/' . $name;
        if (!is_dir($dir)) {
            return false;
        }

        require_once $dir . '/' . $class . '.class.php';

        return null;
    }
}

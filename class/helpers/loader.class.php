<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema Base
@author     Red México
@author     http://www.redmexico.com.mx
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/

class RMLoader
{
    static function api($name, $class){

        if (class_exists($class))
            return false;

        $dir = RMCPATH . '/api/' . $name;
        if (!is_dir($dir))
            return false;

        include_once $dir . '/' . $class . '.class.php';

    return null;
    }
}

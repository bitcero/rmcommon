<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Schooler Pro
 * Description: A module for management of scores and notes for students
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: Private
 * URI: http://www.redmexico.com.mx
 * --------------------------------------------------------------
 */

trait RMProperties
{
    protected static $properties;

    public function __get($name)
    {
        $method = 'get_' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (isset(self::$properties[$name])) {
            return self::$properties[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $method = 'set_' . $name;

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        if (method_exists($this, 'get_'.$name)) {
            throw new RMException(sprintf(__('Property "%s.%s" is read only.', 'rmcommon'), get_class($this), $name));
        }

        self::$properties[$name] = $value;

        return null;
    }
}

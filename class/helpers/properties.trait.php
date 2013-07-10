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

    public function __get( $name ){

        $method = 'get_' . $name;
        if ( method_exists( $this, $method ))
            return $this->$method();

        if ( isset( self::$properties[$name] ) )
            return self::$properties[$name];

    }

    public function __set( $name, $value ){

        $method = 'set_' . $name;

        if ( method_exists( $this, $method ))
            return $this->$method($value);

        self::$properties[$name] = $value;

    }
}
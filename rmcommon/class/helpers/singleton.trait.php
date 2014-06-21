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

trait RMSingleton
{
    private static $instance;

    public static function get(){

        if ( !( self::$instance instanceof self ) )
            self::$instance = new self;

        return self::$instance;

    }

}
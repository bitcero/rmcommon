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

class RMHttpRequest
{
    use RMSingleton;

    static public function request( $key, $type, $default = '' ){

        return self::get_http_parameter( 'request', $key, $type, $default );

    }

    static public function get( $key, $type, $default = '' ){

        return self::get_http_parameter( 'get', $key, $type, $default );

    }

    static public function post( $key, $type, $default = '' ){

        return self::get_http_parameter( 'post', $key, $type, $default );

    }

    static public function put( $key, $type, $default = '' ){

        return self::get_http_parameter( 'put', $key, $type, $default );

    }

    static public function delete( $key, $type, $default = '' ){

        return self::get_http_parameter( 'delete', $key, $type, $default );

    }

    static public function method(){

        return $_SERVER['REQUEST_METHOD'];

    }

    /**
     * Gets the URL parameters passed trough _GET, _POST, _REQUEST methods
     *
     * The key must be a valid parameter name.
     * Type must be a valid data type, e.g. boolean, integer, float, string, number or array
     *
     * @param string $method Method to use (e.g. get, post, request)
     * @param string $key Key name to get
     * @param string $type Value type of the param to get
     * @param mixed $default Value to use when param is not found
     * @return mixed
     */
    static protected function get_http_parameter( $method, $key, $type, $default = '' ){

        if ( $key == '' )
            return;

        $method = strtolower( $method );

        if( !in_array( $method, array( 'get', 'post', 'request', 'put', 'delete' ) ) )
            return;

        if ( $type == '' )
            trigger_error( __('Get values from URL parameters without specify a valid type, can result in security issues. Please consider to specify a type before to get URL params.', 'rmcommon'), E_WARNING );

        switch ( $method ){
            case 'get':
                $_DATA =& $_GET;
                break;
            case 'post':
                $_DATA =& $_POST;
                break;
            case 'request':
                $_DATA =& $_REQUEST;
                break;
            case 'put':
            case 'delete':
                parse_str( file_get_contents( 'php://input' ) );
                if ( isset( ${$key} ) )
                    return self::clean_value( ${$key}, $type );
                else
                    return self::clean_value( $default, $type );
                break;
        }

        if(isset($_DATA[$key]))
            return self::clean_value( $_DATA[$key], $type );
        else
            return self::clean_value( $default, $type );

    }

    /**
     * Converts a value to the correct type
     * @param $value
     * @param $type
     * @return array|bool|float|int|string
     */
    static protected function clean_value($value, $type){

        $return = null;

        switch($type){

            case 'bool':
                $return = (bool) $value;
                break;
            case 'integer':
                $return = (int) $value;
                break;
            case 'float':
                $return = floatval($value);
                break;
            case 'number':
                $return = is_float($value) ? floatval($value) : intval($value);
                break;
            case 'string':
                $return = strval($value);
                break;
            case 'array':
                $return = is_array($value) ? $value : array();
                break;
            default:
                $return = $value;
                break;
        }

        return $return;

    }
}
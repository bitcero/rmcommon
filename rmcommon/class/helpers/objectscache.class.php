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

class ObjectsCache
{

    use RMSingleton;

    private $cache = array();

    /**
     * Set new key in cache
     * @param $module
     * @param $key
     * @param $value
     */
    public function set_cache( $module, $key, $value ){

        $this->cache[ $module ][ $key ] = $value;

    }

    /**
     * Verify if an object is cached. If yes, can return the value or simply bool true
     * @param $module <p>Directory of module.</p>
     * @param $key <p>Identifier for object.</p>
     * @param string $return <p>Kind of value that funtion will return: 'value' or ''.</p>
     * @return object|bool
     */
    public function cached( $module, $key, $return = 'value' ){

        if ( !isset( $this->cache[$module][$key] ) )
            return false;

        if ( $return == 'value' )
            return $this->cache[ $module ][ $key ];

        return true;

    }

}
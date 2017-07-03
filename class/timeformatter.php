<?php
// $Id: timeformatter.php 1056 2012-09-12 15:43:20Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMTimeFormatter
{
    private $time = 0;
    private $format = '';

    /**
    * Initialize this class
    *
    * @param int|string $time <p>Unix timestamp or date in string format</p>
    * @param string $format <p>Format for time (e.g. Created on %M% %d%, %Y%)</p>
    */
    public function __construct($time=0, $format=''){
        $this->time = $time;
        $this->format = $format;
    }

    /**
     * Singleton method
     * @param string $format
     * @return RMTimeFormatter
     */
    public static function get($format = ''){
        static $instance;

        if (!isset($instance)) {
            $instance = new RMTimeFormatter(0, $format);
        }

        return $instance;
    }

    /**
     * Format a given date
     *
     * Given date can be a timestamp (int) or a string date.
     * Example 1:
     *      $tf->format( time() )
     *
     * Example 2:
     *      $tf->format( '2014-05-25' );
     *
     * @param int|string $time <p>Time to format</p>
     * @param string $format <p>This value is optional. Represents the format for the returned value.</p>
     * @return mixed
     */
    public function format($time=0, $format=''){
		global $xoopsConfig;

        if ( $time <= 0 || strpos( $time, "-" ) !== false )
            $time = strtotime( $time );

        $time = xoops_getUserTimestamp($time<=0 ? $this->time : $time, '');

        $format = $format=='' ? $this->format : $format;

        if ($format=='' || $time<0){
            trigger_error(__('You must provide a valid time and format value to use RMTimeFormatter::format() method','rmcommon'));
            return null;
        }

        $find = array(
            '%d%', // Day number
            '%D%', // Day name
            '%m%', // Month number
            '%M%', // Month name
            '%T%', // Month three letters
            '%y%', // Year with two digits (e.g. 04, 05, etc.)
            '%Y%', // Year with four digits (e.g. 2004, 2005, etc.)
            '%h%', // Hour
            '%i%', // Minute
            '%s%' // Second
        );

        $replace = array(
            date('d', $time),
            $this->days($time),
            date('m', $time),
            $this->months($time),
            substr($this->months($time), 0, 3),
            date('y', $time),
            date('Y', $time),
            date('H', $time),
            date('i', $time),
            date('s', $time)
        );



        return str_replace($find, $replace, $format);

    }

    /**
    * Day name for time formatting
    *
    * @param int $time
    * @return string Day name
    */
    public function days($time = 0){

        $time = $time<=0 ? $this->time : $time;
        if($time<=0) return null;

        $days = array(
            __('sunday','rmcommon'),
            __('monday','rmcommon'),
            __('tuesday','rmcommon'),
            __('wednesday','rmcommon'),
            __('thursday','rmcommon'),
            __('friday','rmcommon'),
            __('saturday','rmcommon')
        );

        return $days[date("w", $time)];

    }

    public function months($time=0){
        $time = $time<=0 ? $this->time : $time;
        if($time<=0) return null;

        $months = array(
            __('january', 'rmcommon'),
            __('february', 'rmcommon'),
            __('march', 'rmcommon'),
            __('april', 'rmcommon'),
            __('may', 'rmcommon'),
            __('june', 'rmcommon'),
            __('july', 'rmcommon'),
            __('august', 'rmcommon'),
            __('september', 'rmcommon'),
            __('october', 'rmcommon'),
            __('november', 'rmcommon'),
            __('december', 'rmcommon'),
        );

        return $months[date('n', $time)-1];

    }

    public function ago( $time ){

        if(false == is_numeric($time)){
            $time = strtotime($time);
        }

        if ( $time <= 0 )
            return __('Some time ago', 'rmcommon');

        // Comentario enviado hace menos de un minuto
        if ( $time > time() - 60 )
            return __('A moment ago', 'rmcommon');

        // Comentario enviado hace menos de una hora
        if ( $time > time() - 3600 ){
            $minutes = ceil((time() - $time) / 60);
            return sprintf( __('%u minutes ago', 'rmcommon'), $minutes );
        }

        /**
         * El comentario fue enviado hace menos de 24 horas
         */
        if ( $time > time() - (24 * 3600) ){

            $d1 = date('d', $time);
            $d2 = date('d', time());
            if ( $d2 != $d1 )
                return __('Yesterday', 'rmcommon');

            $hours = ceil((time() - $time) / 3600 );
            return sprintf( __('%u hours ago', 'rmcommon'), $hours );
        }

        // El comentario fue enviado hace menos de 10 días
        if ( $time > time() - (10 * 86400)){

            $days = ceil( time() - $time) / 86400;
            return sprintf( __('%u days ago', 'rmcommon'), $days );

        }

        return $this->format();

    }


}

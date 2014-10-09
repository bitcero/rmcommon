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
    * Singleton Method
    */
    public function get(){
        static $instance;

        if (!isset($instance)) {
            $instance = new RMTimeFormatter();
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

        $time = intval( $time );

        if ( $time <= 0 )
            $time = strtotime( $time );

        $time = xoops_getUserTimestamp($time<=0 ? $this->time : $time, '');

        $format = $format=='' ? $this->format : $format;
        
        if ($format=='' || $time<0){
            trigger_error(__('You must provide a valid time and format value to use RMTimeFormatter::format() method','rmcommon'));
            return;
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
        if($time<=0) return;
        
        $days = array(
            __('Sunday','rmcommon'),
            __('Monday','rmcommon'),
            __('Tuesday','rmcommon'),
            __('Wednesday','rmcommon'),
            __('Thursday','rmcommon'),
            __('Friday','rmcommon'),
            __('Saturday','rmcommon')
        );
        
        return $days[date("w", $time)];
        
    }
    
    public function months($time=0){
        $time = $time<=0 ? $this->time : $time;
        if($time<=0) return;
        
        $months = array(
            __('January', 'rmcommon'),
            __('February', 'rmcommon'),
            __('March', 'rmcommon'),
            __('April', 'rmcommon'),
            __('May', 'rmcommon'),
            __('June', 'rmcommon'),
            __('July', 'rmcommon'),
            __('August', 'rmcommon'),
            __('September', 'rmcommon'),
            __('October', 'rmcommon'),
            __('November', 'rmcommon'),
            __('December', 'rmcommon'),
        );
        
        return $months[date('n', $time)-1];
        
    }
    
    
}

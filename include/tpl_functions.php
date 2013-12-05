<?php
// $Id: tpl_functions.php 949 2012-04-14 04:18:10Z i.bitcero $
// --------------------------------------------------------------
// EXM System
// Content Management System
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: bitc3r0@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* This file contains a set of useful functions for template designers
*/
function tpl_cycle($values, $delimiter = ',', $reset = false){
    static $cycle_index;

    if (trim($values)=='') {
        return;
    }
    
    if(is_array($values)) {
        $cycle_array = $values;
    } else {
        $cycle_array = explode($delimiter,$values);
    }
    
    if(!isset($cycle_index) || $reset) {
    	$cycle_index = 0;
    }
          
    $retval = $cycle_array[$cycle_index];

    if ( $cycle_index >= count($cycle_array) -1 ) {
    	$cycle_index = 0;
    } else {
    	$cycle_index++;
    }

    return $retval;
}

/**
 * @deprecated
* GET Predefined Variable
* @param var Server VAR ($_POST, $_GET, $_SERVER, etc.)
* @param string Value key
* @param mixed Default value to return if the var is not located.
* @return any
*/
function rmc_server_var($from, $key, $default=''){
	$ret = isset($from[$key]) ? $from[$key] : $default;
	return $ret;
}

function showMessage($message, $level=0, $icon = ''){
    $i = isset($_SESSION['cu_redirect_message']) ? count($_SESSION['cu_redirect_messages']) + 1 : 0;
    $_SESSION['cu_redirect_messages'][$i]['text'] = htmlentities($message);
    $_SESSION['cu_redirect_messages'][$i]['level'] = $level;
    $_SESSION['cu_redirect_messages'][$i]['icon'] = $icon;
}


/* DEPRECATED
=========================*/

/**
 * @deprecated
 * @param string $url Pgina en la que se mostrar el error
 * @param string $msg Mensaje de Error
 * @param int $level Indicates the level of the message (error, info, warn, etc.) You can use the constants RMMSG_INFO, RMMSG_WARN... or you can specify your own level number
 * @param string $icon URL for an icon to show in message. This value is used by templates.
 */
function redirectMsg($url, $msg='', $level=5, $icon=''){

    RMUris::redirect_with_message($msg, $url, $level, $icon);

}

/**
 * @deprecated
 */
function xoops_cp_location($location){
    return;
}

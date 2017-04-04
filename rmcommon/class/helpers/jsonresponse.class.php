<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema base
@author     Red México
@author     http://www.redmexico.com.mx
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/

class RMJsonResponse
{
    use RMSingleton, RMProperties;

    static function respond( $message, $type = 'success', $token = 0, $data = array() ){
        global $xoopsSecurity;

        $return['message'] = $message;
        $return['type'] = $type;
        $return['token'] = $token ? $xoopsSecurity->createToken() : '';

        if ( !empty( $data ) )
            $return = array_merge( $return, $data );

        echo json_encode( $return );
        die();

    }
}

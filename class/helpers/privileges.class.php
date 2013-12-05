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

class RMPrivileges
{
    use RMModuleAjax, RMSingleton;
    /**
     * Determines if the current user have access to specified action
     * @param string $module Module name
     * @param string $action Action identifier
     * @param string $method 'ajax' or ''
     * @param bool $redirect
     * @return mixed
     */
    public function verify($module, $action, $method = '', $redirect = true){

        global $xoopsUser, $xoopsDB;

        if (!$xoopsUser){
            if ( $redirect )
                $this->response( $method );
            else
                return false;
        }


        if ( $xoopsUser->uid() == SMARTB_SUPER_ADMIN )
            return true;

        $groups = $xoopsUser->getGroups();

        $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_users_permissions") ." WHERE
                `group` IN (" . implode( ",", $groups ) . ") AND element='$module' AND
                `key`='$action'";

        list( $num ) = $xoopsDB->fetchRow( $xoopsDB->query( $sql ) );

        if ( $num > 0 )
            return true;

        if ($redirect)
            self::response( $method );
        else
            return false;


    }

    private function response( $method ){

        if ( $method == 'ajax' ){
            $this->prepare_ajax_response();
            $this->ajax_response(
                __('¡No tienes permiso para realizar esta operación!', 'rmcommon'),
                1, 0, array(
                    'action' => 'go-to',
                    'url' => XOOPS_URL
                )
            );
        } else {

            RMUris::redirect_with_message( __('¡No tienes permiso para realizar esta operación!', 'rmcommon'), XOOPS_URL, RMMSG_WARN, 'icon-warning' );

        }


    }
}
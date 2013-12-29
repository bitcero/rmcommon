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
    static public function verify($module, $action, $method = '', $redirect = true){

        global $xoopsUser, $xoopsDB;

        $mod = RMModules::load_module( $module );

        if (!$xoopsUser){
            $groups = array( XOOPS_GROUP_ANONYMOUS );
        } else {

            if ( $xoopsUser->isAdmin( $mod->getVar( 'mid' ) ) )
                return true;

            $groups = $xoopsUser->getGroups();

        }

        $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_rmcommon_permissions") ." WHERE
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

    /**
     * Retrieves the permissions defined by module
     * <strong>Use:</strong>
     * <code>$permissions = RMPrivileges::module_privileges( 'dirname' );</code>
     * Return:
     * <code>Array
     * (
     *     [item1] => Array
     *        (
     *            [caption] => Item Caption
     *            [default] => allow or deny
     *        )
     *     [item2] => Array
     *        (
     *            [caption] => Item 2 Caption
     *            [default] => allow or deny
     *        )
     *     [item3] => Array
     *        (
     *            [caption] => Item 3 Caption
     *            [default] => allow or deny
     *        )
     * )</code>
     * @param string $directory MOdule directory
     * @return array|bool|mixed
     */
    static public function module_permissions( $directory ){

        if ( $directory == '' ) return false;

        $module = RMModules::load_module( $directory );

        if ( !$module )
            return false;

        if ( !$module->getInfo( 'permissions' ) )
            return false;

        $file = XOOPS_ROOT_PATH . '/modules/' . $directory . '/' . $module->getInfo( 'permissions' );

        if ( !is_file( $file ) )
            return false;

        $permissions = include( $file );

        return $permissions;

    }

    static public function read_permissions( $directory, $group ){
        global $xoopsDB;

        if ( $directory == '' ) return false;

        $module = RMModules::load_module( $directory );

        if ( !$module )
            return false;

        // Permissions on DB
        $sql = "SELECT * FROM " . $xoopsDB->prefix("mod_rmcommon_permissions") ." WHERE
                `group` = $group AND element='$directory'";

        $result = $xoopsDB->query( $sql );
        $permissions = new stdClass();

        while ( $row = $xoopsDB->fetchArray( $result ) ){

            $permissions->$row['key'] = 1;

        }

        return $permissions;


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

            RMUris::redirect_with_message( __('¡No tienes permiso para realizar esta operación!', 'rmcommon'), XOOPS_URL, RMMSG_WARN, 'fa fa-warning' );

        }

    }

}
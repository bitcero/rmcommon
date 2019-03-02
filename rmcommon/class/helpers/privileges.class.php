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
    use RMModuleAjax;

    /**
     * Loads all user permissions and stores in a stdClass
     * that will functioning as cache
     */
    private function load_user_permissions()
    {
        global $xoopsUser, $xoopsDB;

        $privileges = UserPrivileges::get();
        $privileges->allowed = array();

        // User must not have any permission
        if (!$xoopsUser) {
            return;
        }

        $groups = $xoopsUser->getGroups();

        $sql = "SELECT * FROM " . $xoopsDB->prefix("mod_rmcommon_permissions") ." WHERE
                `group` IN (" . implode(",", $groups) . ")";
        $result = $xoopsDB->query($sql);

        while ($row = $xoopsDB->fetchArray($result)) {
            $privileges->allowed[$row['element']][$row['key']] = 'allow';
        }
    }

    /**
     * Determines if the current user have access to specified action
     * @param string $module Module name
     * @param string $action Action identifier
     * @param string $method 'ajax' or ''
     * @param bool $redirect
     * @return mixed
     */
    public static function verify($module, $action, $method = 'ajax', $redirect = true)
    {
        global $xoopsUser;

        if (!$xoopsUser) {
            if ($redirect) {
                self::response($method);
            } else {
                return false;
            }
        }

        // Super admin
        if ($xoopsUser->uid() == 1) {
            return true;
        }

        $privileges = UserPrivileges::get();

        if (empty($privileges->allowed)) {
            self::load_user_permissions();
        }


        if (isset($privileges->allowed[$module][$action])) {
            return true;
        }

        if ($redirect) {
            self::response($method);
        } else {
            return false;
        }

        return null;
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
    public static function module_permissions($directory)
    {
        if ($directory == '') {
            return false;
        }

        $module = RMModules::load_module($directory);

        if (!$module) {
            return false;
        }

        if (!$module->getInfo('permissions')) {
            return false;
        }

        $file = XOOPS_ROOT_PATH . '/modules/' . $directory . '/' . $module->getInfo('permissions');

        if (!is_file($file)) {
            return false;
        }

        $permissions = include $file;

        return $permissions;
    }

    public static function read_permissions($directory, $group)
    {
        global $xoopsDB;

        if ($directory == '') {
            return false;
        }

        $module = RMModules::load_module($directory);

        if (!$module) {
            return false;
        }

        // Permissions on DB
        $sql = "SELECT * FROM " . $xoopsDB->prefix("mod_rmcommon_permissions") ." WHERE
                `group` = $group AND element='$directory'";

        $result = $xoopsDB->query($sql);
        $permissions = new stdClass();

        while ($row = $xoopsDB->fetchArray($result)) {
            $permissions->{$row['key']} = 1;
        }

        return $permissions;
    }

    private function response($method)
    {
        global $common;

        if ($method == 'ajax') {
            $common->ajax()->prepare();
            $common->ajax()->response(
                __('You don\'t have required rights to do this action!', 'rmcommon'),
                1,
                0,
                array(
                    'goto' => XOOPS_URL
                )
            );
        } else {
            RMUris::redirect_with_message(__('You don\'t have required rights to do this action!', 'rmcommon'), XOOPS_URL, RMMSG_WARN, 'fa fa-warning');
        }
    }
    
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new RMPrivileges();
        }

        return $instance;
    }
}


class UserPrivileges
{
    use RMSingleton;

    /**
     * Stores all privileges for current user
     * @var array
     */
    public $allowed;
}

<?php
// $Id$
// --------------------------------------------------------------
// Common Utilities 2
// A modules framework by Red Mexico
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: http://www.redmexico.com.mx
// --------------------------------------------------------------

/**
 * This files contains the class that allows to work with configurations and other related operations.
 */

class RMSettings
{

    private static $plugin_settings = array();
    private static $modules_settings = array();

    /**
     * Get the current settings for Common Utilities
     * This method is a replace for deprecated RMSettings::cu_settings() method
     *
     * @param string $name
     * @return stdClass
     */
    static function cu_settings($name=''){
        global $cuSettings;

        if (!isset($cuSettings)){

            $cuSettings = new stdClass();

            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $sql = "SELECT mid FROM ".$db->prefix("modules")." WHERE dirname='rmcommon'";
            list($id) = $db->fetchRow($db->query($sql));

            include_once XOOPS_ROOT_PATH . '/kernel/object.php';
            include_once XOOPS_ROOT_PATH . '/kernel/configitem.php';
            include_once XOOPS_ROOT_PATH . '/class/criteria.php';
            include_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
            $ret = array();
            $result = $db->query("SELECT * FROM ".$db->prefix("config")." WHERE conf_modid='$id'");

            while($row = $db->fetchArray($result)){
                $config = new XoopsConfigItem();
                $config->assignVars($row);
                $cuSettings->{$config->getVar('conf_name')} = $config->getConfValueForOutput();
            }

        }

        $name = trim($name);
        if($name!=''){
            if( isset( $cuSettings->{$name} ) ) return $cuSettings->{$name};
        }

        return $cuSettings;
    }

    /**
     * Retrieves the settings for a given plugin
     *
     * @param string $dir Plugin's directory
     * @param bool $values
     * @return array
     */
    static function plugin_settings($dir, $values = false){

        if ($dir=='') return;

        if (!isset(self::$plugin_settings[$dir])){

            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_settings")." WHERE element='$dir'";
            $result = $db->query($sql);
            if($db->getRowsNum($result)<=0) return;
            $configs = array();
            while ($row = $db->fetchArray($result)){
                $configs[$row['name']] = $row;
            }

            $configs = self::option_value_output($configs);
            self::$plugin_settings[$dir] = $configs;

        }

        if (!$values) return (object) self::$plugin_settings[$dir];

        $ret = array();
        foreach(self::$plugin_settings[$dir] as $name => $conf){
            $ret[$name] = $conf['value'];
        }

        return (object) $ret;

    }

    /**
     * Format the settings values value according to their types
     * @param array $settings Settings array
     * @return mixed
     */

    private static function option_value_output( $settings ){

        foreach ( $settings as $name => $data ){

            switch ($data['valuetype']) {
                case 'int':
                    $settings[$name]['value'] = intval($data['value']);
                    break;
                case 'array':
                    $settings[$name]['value'] = unserialize($data['value']);
                    break;
                case 'float':
                    $settings[$name]['value'] = floatval($data['value']);
                    break;
                case 'textarea':
                    $settings[$name]['value'] = stripSlashes($data['value']);
                    break;
            }
        }

        return $settings;
    }

    /**
     * Retrieves the configuration for a given module.
     *
     * <pre>
     * $settings = RMSettings::module_settings('mywords');
     * $option_value = RMSettings::module_settings('mywords', 'basepath');
     * </pre>
     *
     * @param string $directory Directory name where module resides in
     * @param string $option Name of the option to retrieve (if any)
     * @return mixed
     */
    public static function module_settings($directory, $option=''){
        global $xoopsModuleConfig, $xoopsModule;

        if ( isset( self::$modules_settings[$directory] ) ){

            if( $option != '' & isset( self::$modules_settings[$directory][$option] ) )
                return self::$modules_settings[$directory][$option];

            return (object) self::$modules_settings[$directory];

        }

        if ( isset( $xoopsModuleConfig ) && ( is_object( $xoopsModule ) && $xoopsModule->getVar( 'dirname' ) == $directory && $xoopsModule->getVar( 'isactive' ) ) ) {

            self::$modules_settings[$directory] = $xoopsModuleConfig;

            if( $option != '' && isset( $xoopsModuleConfig[$option] ) )
                return $xoopsModuleConfig[$option];
            else
                return (object) $xoopsModuleConfig;

        } else {
            $module_handler =& xoops_gethandler( 'module' );
            $module = $module_handler->getByDirname( $directory );
            $config_handler =& xoops_gethandler( 'config' );
            if ($module) {

                $moduleConfig =& $config_handler->getConfigsByCat( 0, $module->getVar('mid') );
                self::$modules_settings[$directory] = $moduleConfig;

                if($option != '' && isset($moduleConfig[$option]))
                    return $moduleConfig[$option];
                else
                    return (object) $moduleConfig;

            }
        }
    }

}
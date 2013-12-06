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

    /**
     * Prepares the form field that will be shown on settings form
     * and returns the HTML code.
     * <br><br>
     * <p><strong>Usage:</strong></p>
     * <code>echo RMSettings::render_field( string 'field_id', array $field );</code>
     *
     * @param stdClass $field <p>An object with all field values, including caption, id, description, type, value, etc.</p>
     * @return string
     */
    public static function render_field( $field ){

        if ( empty( $field ) )
            return;

        $tc = TextCleaner::getInstance();

        switch ( $field->field ) {

            case 'textarea':
                if ($field->type == 'array') {
                    // this is exceptional.. only when value type is arrayneed a smarter way for this
                    $ele = ($field->value != '') ? new RMFormTextArea($field->caption, $field->id, $tc->specialchars(implode('|', $field->value)), 5, 50) : new RMFormTextArea($field->title, $field->id, '', 5, 50);
                } else {
                    $ele = new RMFormTextArea($field->caption, $field->id, $tc->specialchars($field->value), 5, 50);
                }
                break;

            case 'select':
                $ele = new RMFormSelect($field->caption, $field->id, 0, array($field->value));
                foreach( $field->options as $value => $caption ){
                    $ele->addOption( $value, $caption );
                }
                break;
/*
            case 'select_multi':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                $options = $config_handler->getConfigOptions(new Criteria('conf_id', $config[$i]->getVar('conf_id')));
                $opcount = count($options);
                for ($j = 0; $j < $opcount; $j++) {
                    $optval = defined($options[$j]->getVar('confop_value')) ? constant($options[$j]->getVar('confop_value')) : $options[$j]->getVar('confop_value');
                    $optkey = defined($options[$j]->getVar('confop_name')) ? constant($options[$j]->getVar('confop_name')) : $options[$j]->getVar('confop_name');
                    $ele->addOption($optval, $optkey);
                }
                break;

            case 'yesno':
                $ele = new XoopsFormRadioYN($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), _YES, _NO);
                break;

            case 'theme':
            case 'theme_multi':
                $ele = ($config[$i]->getVar('conf_formtype')
                    != 'theme_multi') ? new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput()) : new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput(), 5, true);
                require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
                $dirlist = XoopsLists::getThemesList();
                if (!empty($dirlist)) {
                    asort($dirlist);
                    $ele->addOptionArray($dirlist);
                }
                $valueForOutput = $config[$i]->getConfValueForOutput();
                $form->addElement(new XoopsFormHidden('_old_theme', (is_array($valueForOutput) ? $valueForOutput[0] : $valueForOutput)));
                break;

            case 'tplset':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $tplset_handler =& xoops_gethandler('tplset');
                $tplsetlist = $tplset_handler->getList();
                asort($tplsetlist);
                foreach ($tplsetlist as $key => $name) {
                    $ele->addOption($key, $name);
                }
                // old theme value is used to determine whether to update cache or not. kind of dirty way
                $form->addElement(new XoopsFormHidden('_old_theme', $config[$i]->getConfValueForOutput()));
                break;

            case 'cpanel':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                xoops_load("cpanel", "system");
                $list = XoopsSystemCpanel::getGuis();
                $ele->addOptionArray( $list );
                break;

            case 'timezone':
                $ele = new XoopsFormSelectTimezone($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;

            case 'language':
                $ele = new XoopsFormSelectLang($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                break;

            case 'startpage':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $module_handler =& xoops_gethandler('module');
                $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
                $criteria->add(new Criteria('isactive', 1));
                $moduleslist = $module_handler->getList($criteria, true);
                $moduleslist['--'] = _MD_AM_NONE;
                $ele->addOptionArray($moduleslist);
                break;

            case 'group':
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                break;

            case 'group_multi':
                $ele = new XoopsFormSelectGroup($title, $config[$i]->getVar('conf_name'), true, $config[$i]->getConfValueForOutput(), 5, true);
                break;

            // RMV-NOTIFY - added 'user' and 'user_multi'
            case 'user':
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 1, false);
                break;

            case 'user_multi':
                $ele = new XoopsFormSelectUser($title, $config[$i]->getVar('conf_name'), false, $config[$i]->getConfValueForOutput(), 5, true);
                break;

            case 'module_cache':
                $module_handler =& xoops_gethandler('module');
                $modules = $module_handler->getObjects(new Criteria('hasmain', 1), true);
                $currrent_val = $config[$i]->getConfValueForOutput();
                $cache_options = array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK);
                if (count($modules) > 0) {
                    $ele = new XoopsFormElementTray($title, '<br />');
                    foreach (array_keys($modules) as $mid) {
                        $c_val = isset($currrent_val[$mid]) ? intval($currrent_val[$mid]) : null;
                        $selform = new XoopsFormSelect($modules[$mid]->getVar('name'), $config[$i]->getVar('conf_name')."[$mid]", $c_val);
                        $selform->addOptionArray($cache_options);
                        $ele->addElement($selform);
                        unset($selform);
                    }
                } else {
                    $ele = new XoopsFormLabel($title, _MD_AM_NOMODULE);
                }
                break;

            case 'site_cache':
                $ele = new XoopsFormSelect($title, $config[$i]->getVar('conf_name'), $config[$i]->getConfValueForOutput());
                $ele->addOptionArray(array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK));
                break;

            case 'password':
                $myts =& MyTextSanitizer::getInstance();
                $ele = new XoopsFormPassword($title, $config[$i]->getVar('conf_name'), 50, 255, $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;

            case 'color':
                $myts =& MyTextSanitizer::getInstance();
                $ele = new XoopsFormColorPicker($title, $config[$i]->getVar('conf_name'), $myts->htmlspecialchars($config[$i]->getConfValueForOutput()));
                break;

            case 'hidden':
                $myts =& MyTextSanitizer::getInstance();
                $ele = new XoopsFormHidden( $config[$i]->getVar('conf_name'), $myts->htmlspecialchars( $config[$i]->getConfValueForOutput() ) );
                break;*/

            case 'textbox':
            default:
                $ele = new RMFormText($field->caption, $field->id, 50, 255, $tc->specialchars($field->value));
                break;

        }

        return $ele->render();

    }

}
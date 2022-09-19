<?php
// $Id$
// --------------------------------------------------------------
// Common Utilities 2
// A modules framework by Red Mexico
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: https://bitcero.dev
// --------------------------------------------------------------

/**
 * This files contains the class that allows to work with configurations and other related operations.
 */
class RMSettings
{
    private static $plugin_settings = [];
    private static $modules_settings = [];

    /**
     * Get the current settings for Common Utilities
     * This method is a replace for deprecated RMSettings::cu_settings() method
     *
     * @param string $name
     * @return stdClass
     */
    public static function cu_settings($name = '')
    {
        global $cuSettings;

        if (!isset($cuSettings)) {
            $cuSettings = new stdClass();

            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $sql = 'SELECT mid FROM ' . $db->prefix('modules') . " WHERE dirname='rmcommon'";
            list($id) = $db->fetchRow($db->query($sql));

            require_once XOOPS_ROOT_PATH . '/kernel/object.php';
            require_once XOOPS_ROOT_PATH . '/kernel/configitem.php';
            require_once XOOPS_ROOT_PATH . '/class/criteria.php';
            require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
            $ret = [];
            $result = $db->query('SELECT * FROM ' . $db->prefix('config') . " WHERE conf_modid='$id'");

            while (false !== ($row = $db->fetchArray($result))) {
                $config = new XoopsConfigItem();
                $config->assignVars($row);
                $cuSettings->{$config->getVar('conf_name')} = $config->getConfValueForOutput();
            }
        }

        $name = trim($name);
        if ('' != $name && isset($cuSettings->{$name})) {
            return $cuSettings->{$name};
        } elseif ('' != $name) {
            return [];
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
    public static function plugin_settings($dir, $values = false)
    {
        if ('' == $dir) {
            return null;
        }

        if (!isset(self::$plugin_settings[$dir])) {
            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $sql = 'SELECT * FROM ' . $db->prefix('mod_rmcommon_settings') . " WHERE element='$dir'";
            $result = $db->query($sql);
            if ($db->getRowsNum($result) <= 0) {
                return null;
            }
            $configs = [];
            while (false !== ($row = $db->fetchArray($result))) {
                $configs[$row['name']] = $row;
            }

            $configs = self::option_value_output($configs);
            self::$plugin_settings[$dir] = $configs;
        }

        if (!$values) {
            return (object)self::$plugin_settings[$dir];
        }

        $ret = [];
        foreach (self::$plugin_settings[$dir] as $name => $conf) {
            $ret[$name] = $conf['value'];
        }

        return (object)$ret;
    }

    /**
     * Format the settings values value according to their types
     * @param array $settings Settings array
     * @return mixed
     */
    private static function option_value_output($settings)
    {
        foreach ($settings as $name => $data) {
            switch ($data['valuetype']) {
                case 'int':
                    $settings[$name]['value'] = (int)$data['value'];
                    break;
                case 'array':
                    $settings[$name]['value'] = unserialize($data['value']);
                    break;
                case 'float':
                    $settings[$name]['value'] = (float)$data['value'];
                    break;
                case 'textarea':
                    $settings[$name]['value'] = stripslashes($data['value']);
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
    public static function module_settings($directory, $option = '')
    {
        global $xoopsModuleConfig, $xoopsModule;

        if (isset(self::$modules_settings[$directory])) {
            if ('' != $option & isset(self::$modules_settings[$directory][$option])) {
                return self::$modules_settings[$directory][$option];
            }

            return (object)self::$modules_settings[$directory];
        }

        if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $directory && $xoopsModule->getVar('isactive'))) {
            self::$modules_settings[$directory] = $xoopsModuleConfig;

            if ('' != $option && isset($xoopsModuleConfig[$option])) {
                return $xoopsModuleConfig[$option];
            }

            return (object)$xoopsModuleConfig;
        }
        $moduleHandler = xoops_getHandler('module');
        $module = $moduleHandler->getByDirname($directory);
        $configHandler = xoops_getHandler('config');
        if ($module) {
            $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
            self::$modules_settings[$directory] = $moduleConfig;

            if ('' != $option && isset($moduleConfig[$option])) {
                return $moduleConfig[$option];
            }

            return (object)$moduleConfig;
        }

        return null;
    }

    public function setValue($directory, $option, $value)
    {
        global $xoopsModule, $xoopsDB;

        if ('' == $directory || '' == $option) {
            return false;
        }

        if ($xoopsModule && $xoopsModule->getVar('dirname') == $directory) {
            $mod = $xoopsModule;
        } else {
            $moduleHandler = xoops_getHandler('module');
            $mod = $moduleHandler->getByDirname($directory);
        }

        $item = new Rmcommon_Config_Item($option, $mod->getVar('mid'));
        if ($item->isNew()) {
            return false;
        }

        $item->set_value($value);

        if ($item->save()) {
            return true;
        }

        return false;
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
    public static function render_field($field)
    {
        if (empty($field)) {
            return null;
        }

        $tc = TextCleaner::getInstance();

        $field->caption = $tc->clean_disabled_tags($field->caption);

        switch ($field->field) {
            case 'textarea':
                if ('array' == $field->type) {
                    // this is exceptional.. only when value type is arrayneed a smarter way for this
                    $ele = ('' != $field->value) ? new RMFormTextArea($field->caption, $field->name, 4, 45, $tc->specialchars(implode('|', $field->value))) : new RMFormTextArea($field->title, $field->name, 4, 45);
                } else {
                    $ele = new RMFormTextArea($field->caption, $field->name, 4, 50, $tc->specialchars($field->value));
                }
                break;
            case 'select':
                $ele = new RMFormSelect($field->caption, $field->name, 0, [$field->value]);
                foreach ($field->options as $caption => $value) {
                    $ele->addOption($value, defined($caption) ? constant($caption) : $caption);
                }
                break;
            case 'select_multi':
                $ele = new RMFormSelect($field->caption, $field->name, 1, [$field->value]);
                $options = $field->options;
                foreach ($options as $value => $caption) {
                    $value = defined($value) ? constant($value) : $value;
                    $caption = defined($caption) ? constant($caption) : $caption;
                    $ele->addOption($value, $caption);
                }
                break;
            case 'yesno':
                $ele = new RMFormYesNo($field->caption, $field->name, $field->value);
                break;
            case 'radio':
                $ele = new RMFormRadio([
                    'caption' => $field->caption,
                    'display' => 'list',
                    'name' => $field->name,
                ]);
                $options = $field->options;
                foreach ($options as $value => $caption) {
                    $value = defined($value) ? constant($value) : $value;
                    $caption = defined($caption) ? constant($caption) : $caption;
                    $ele->addOption($caption, $value, $value == $field->value ? 'selected' : '');
                }
                break;
            case 'checkbox':
                $ele = new RMFormCheck([
                    'caption' => $field->caption,
                    'display' => 'inline',
                    'name' => $field->name . ('array' == $field->type ? '[]' : ''),
                ]);
                $options = $field->options;
                foreach ($options as $value => $caption) {
                    $value = defined($value) ? constant($value) : $value;
                    $caption = defined($caption) ? constant($caption) : $caption;

                    if ('array' == $field->type) {
                        $checked = in_array($value, $field->value, true);
                    } else {
                        $checked = $value == $field->value;
                    }

                    $ele->addOption($caption, $caption['name'], $value, $checked ? 'selected' : '');
                }
                break;
            case 'theme':
            case 'theme_multi':

                $ele = new RMFormTheme($field->caption, $field->name, 'theme_multi' == $field->field ? 1 : 0, 0, $field->value);
                break;
            case 'cu-theme':
            case 'cu-theme-multi':
                $ele = new RMFormTheme($field->caption, $field->name, 'cu-theme-multi' == $field->field ? 1 : 0, 0, $field->value, 1, 'GUI');
                break;
            case 'tplset':
                $ele = new RMFormSelect($field->caption, $field->name, 0, [$field->value]);

                $tplsetHandler = xoops_getHandler('tplset');
                $tplsetlist = $tplsetHandler->getList();
                asort($tplsetlist);
                foreach ($tplsetlist as $key => $name) {
                    $ele->addOption($key, $name);
                }
                break;
            case 'cpanel':
                $ele = new RMFormSelect($field->caption, $field->name, 0, [$field->value]);
                xoops_load('cpanel', 'system');
                $list = XoopsSystemCpanel::getGuis();
                $ele->addOptionArray($list);

                break;
            case 'timezone':
                $ele = new RMFormTimeZoneField($field->caption, $field->name, 0, 0, $field->value);

                break;
            case 'language':
            case 'language_multi':
                $langs = XoopsLists::getLangList();
                $ele = new RMFormSelect($field->caption, $field->name, 'language_multi' == $field->field ? 1 : 0, $field->value);
                foreach ($langs as $caption => $value) {
                    $ele->addOption($value, $caption);
                }

                break;
            case 'cu-language':
            case 'cu-language-multi':

                $ele = new RMFormLanguageField($field->caption, $field->name, 'cu-language-multi' == $field->field ? 1 : 0, 0, [$field->value]);
                break;
            case 'startpage':
                $ele = new RMFormSelect($field->caption, $field->name, 0, [$field->value]);
                $moduleHandler = xoops_getHandler('module');
                $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
                $criteria->add(new Criteria('isactive', 1));
                $moduleslist = $moduleHandler->getList($criteria, true);
                $moduleslist['--'] = _MD_AM_NONE;
                $ele->addOptionsArray($moduleslist);

                break;
            case 'group':
            case 'group_multi':
                $ele = new RMFormGroups($field->caption, $field->name, 'group_multi' == $field->field ? 1 : 0, 0, 1, $field->value);
                break;
            case 'user':
            case 'user_multi':
                $ele = new RMFormUser($field->caption, $field->name, 'user_multi' == $field->field ? 1 : 0, $field->value);
                break;
            case 'module_cache':

                $ele = new RMFormCacheModuleField($field->caption, $field->name, $field->value);
                break;
            case 'site_cache':
                $ele = new RMFormSelect($field->caption, $field->name, 0, $field->value);

                $ele->addOptionArray([
                    '0' => __('No cache', 'rmcommon'),
                    '30' => sprintf(__('%u seconds', 'rmcommon'), 30),
                    '60' => __('1 minute', 'rmcommon'),
                    '300' => sprintf(__('%u minutes', 'rmcommon'), 5),
                    '1800' => sprintf(__('%u minutes', 'rmcommon'), 30),
                    '3600' => __('One hour', 'rmcommon'),
                    '18000' => sprintf(__('%u hours', 'rmcommon'), 5),
                    '86400' => __('One day', 'rmcommon'),
                    '259200' => sprintf(__('%u days', 'rmcommon'), 3),
                    '604800' => __('One week', 'rmcommon'),
                ]);
                break;
            case 'password':
                $ele = new RMFormText($field->caption, $field->name, 50, 255, $field->value, true);

                break;
            case 'hidden':
                $ele = new RMFormHidden($field->name, $field->value);

                break;
            case 'modules-rewrite':
                $ele = new RMFormRewrite($field->caption, $field->name, $field->value);
                break;
            case 'textbox':
            default:
                $ele = new RMFormText($field->caption, $field->name, 50, 255, $tc->specialchars($field->value));
                break;
        }

        /**
         * Allow to plugins and other modules create new form
         * elements types in settings form
         */
        $ele = RMEvents::get()->trigger('rmcommon.load.form.field', $ele, $field);
        $ele->set('id', $field->id);

        $skipControls = ['radio', 'checkbox', 'yesno'];

        if (!in_array($field->field, $skipControls, true)) {
            $ele->add('class', 'form-control');
        }

        return $ele->render();
    }

    public static function write_rewrite_js()
    {
        $paths = self::cu_settings('modules_path');

        $content = "var cu_modules = {\n";

        foreach ($paths as $module => $path) {
            $content .= "$module: '" . rtrim($path, '/') . "',\n";
        }

        $content .= "};\n";

        RMTemplate::getInstance()->add_inline_script($content, 0);
    }
}

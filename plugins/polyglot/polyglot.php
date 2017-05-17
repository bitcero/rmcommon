<?php

/**
 * Polyglot for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      polyglot
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

class PolyglotCUPlugin extends \RMIPlugin
{
    private $file = '';
    private $languages = [];
    private $baseLang = '';
    private $elements = [];

    /**
     * Stores relations between XOOPS languages folders
     * and Common Utilities languages files
     * @var array
     */
    private $langRelations = [];

    /**
     * PolyglotCUPlugin constructor.
     */
    public function __construct()
    {

        // Load language
        //load_plugin_locale('polyglot', '', 'rmcommon');

        $this->info = array(
            'name' => __('Polyglot', 'lightbox'),
            'description' => __('Multilingual plugin for Common Utilities and XOOPS.', 'lightbox'),
            'version' => array('major' => 0, 'minor' => 53, 'revision' => 38, 'stage' => -3, 'name' => 'Polyglot'),
            'author' => 'Eduardo Cortés',
            'email' => 'i.bitcero@gmail.com',
            'web' => 'http://eduardocortes.mx',
            'dir' => 'polyglot',
            //'updateurl' => 'http://www.xoopsmexico.net/modules/vcontrol/',
            'hasmain' => true
        );

        $this->file = XOOPS_CACHE_PATH . '/polyglot-langs.json';

        // Load languages
        if($this->isActive()){
            $this->getLanguages();
        }

        /**
         * Set relations between XOOPS languages and CU languages
         */
        if (!empty($this->languages)) {

            foreach ($this->languages as $code => $lang) {
                $this->langRelations[$lang['directory']] = $code;

                if (array_key_exists('type', $lang) && $lang['type'] == 'base') {
                    $this->baseLang = $code;
                }
            }

        }

    }

    /**
     * Create table to enable translation support
     * @return bool
     */
    public function on_install()
    {
        global $xoopsDB;

        $rmcommon = RMModules::load_module('rmcommon');

        if (version_compare(RMFormat::version($rmcommon->getInfo('rmversion')), '2.3', '<')) {
            $this->addError(__('Common Utilities version must be at least 2.3', 'polyglot'));
            return false;
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `' . $xoopsDB->prefix('plugin_polyglot_elements') . '` (
                `id_element` int(11) NOT NULL,
                  `name` varchar(100) NOT NULL,
                  `type` varchar(6) NOT NULL DEFAULT \'module\'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $xoopsDB->queryF($sql);

        $sql = 'ALTER TABLE `' . $xoopsDB->prefix('plugin_polyglot_elements') . '`
                 ADD PRIMARY KEY (`id_element`), ADD KEY `name` (`name`,`type`);';
        $xoopsDB->queryF($sql);

        $sql = 'ALTER TABLE `' . $xoopsDB->prefix('plugin_polyglot_elements') . '`
                MODIFY `id_element` int(11) NOT NULL AUTO_INCREMENT;';

        $xoopsDB->queryF($sql);

        return true;
    }

    public function on_uninstall()
    {
        global $xoopsDB;

        // Deletes tables
        foreach ($this->languages as $code => $lang) {
            $sql = "DROP TABLE " . $xoopsDB->prefix("plugin_polyglot_{$code}_shorts");
            $xoopsDB->queryF($sql);
            $sql = "DROP TABLE " . $xoopsDB->prefix("plugin_polyglot_{$code}_longs");
            $xoopsDB->queryF($sql);
        }

        $xoopsDB->queryF("DROP TABLE " . $xoopsDB->prefix('plugin_polyglot_elements'));

        // Deletes languages files
        unlink($this->file);

        // Delete cookie
        setcookie('lang', null, -1, '/');

        return true;
    }

    public function on_update()
    {
        return true;
    }

    public function on_activate($q)
    {
        return true;
    }

    public function options()
    {

        $languages = $this->getLanguages();

        $select = [];
        foreach($languages as $code => $lang){
            $select[$code] = $lang['name'];
        }

        $options['default'] = array(
            'caption'   =>  __('Default language','polyglot'),
            'desc'      =>  __('Select the language that will be used when users browser language does not match with any language registered in Polyglot.', 'polyglot'),
            'fieldtype'      =>  'select',
            'valuetype' =>  'text',
            'options' => $select,
            'value'   =>  $this->baseLanguage()
        );

        return $options;

    }

    /**
     * Control Panel
     */
    public function main()
    {
        global $common;

        $page = $common->httpRequest()->request('page', 'string', '');
        $common->template()->add_help(__('Polyglot Docs', 'polyglot'), 'https://docs.redmexico.com.mx/docs/bitcero/plugin-polyglot/');

        if ('adjust' == $page) {
            require $common->path('plugins/polyglot/cp/adjust.php');
        } else {
            require $common->path('plugins/polyglot/cp/dashboard.php');
        }
    }

    public function addLanguageTable($code)
    {
        global $common, $xoopsDB;

        $sql = "CREATE TABLE IF NOT EXISTS `" . $xoopsDB->prefix("plugin_polyglot_{$code}_shorts") . "` (
                `id_var` int(11) NOT NULL,
                  `element` int(11) NOT NULL,
                  `object` varchar(100) NOT NULL,
                  `object_id` int(11) NOT NULL,
                  `var` varchar(100) NOT NULL,
                  `value` varchar(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        if (!$common->db()->queryF($sql)) {
            $this->addError($xoopsDB->error());
            return false;
        }

        $sql = "ALTER TABLE `" . $xoopsDB->prefix("plugin_polyglot_{$code}_shorts") . "`
                 ADD PRIMARY KEY (`id_var`), ADD KEY `element` (`element`,`object`,`object_id`,`var`);";

        $xoopsDB->queryF($sql);

        $sql = "ALTER TABLE `" . $xoopsDB->prefix("plugin_polyglot_{$code}_shorts") . "`
                MODIFY `id_var` int(11) NOT NULL AUTO_INCREMENT;";

        $xoopsDB->queryF($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `" . $xoopsDB->prefix("plugin_polyglot_{$code}_longs") . "` (
                `id_var` int(11) NOT NULL,
                  `element` int(11) NOT NULL,
                  `object` varchar(100) NOT NULL,
                  `object_id` int(11) NOT NULL,
                  `var` varchar(100) NOT NULL,
                  `value` LONGTEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        if (!$common->db()->queryF($sql)) {
            $this->addError($xoopsDB->error());
            $xoopsDB->queryF("DROP TABLE " . $xoopsDB->prefix("plugin_polyglot_{$code}_shorts"));
            return false;
        }

        $sql = "ALTER TABLE `" . $xoopsDB->prefix("plugin_polyglot_{$code}_longs") . "`
                 ADD PRIMARY KEY (`id_var`), ADD KEY `element` (`element`,`object`,`object_id`,`var`);";

        $xoopsDB->queryF($sql);

        $sql = "ALTER TABLE `" . $xoopsDB->prefix("plugin_polyglot_{$code}_longs") . "`
                MODIFY `id_var` int(11) NOT NULL AUTO_INCREMENT;";

        $xoopsDB->queryF($sql);

        return true;

    }

    /**
     * Get the url for a country flag based in the country code
     * @param $country Country code
     * @return null|string
     */
    public function countryFlag($country)
    {
        if ('' == trim($country)) {
            return null;
        }

        $file = '/flags/' . $country . '.png';

        if (file_exists($this->path() . $file)) {
            return $this->url() . $file;
        }

        return null;
    }

    /**
     * Get all languages registered in config file
     * @return array|mixed
     */
    public function getLanguages()
    {

        if (empty($this->languages)) {
            $this->languages = json_decode(file_get_contents($this->file), true);
        }

        return $this->languages;

    }

    /**
     * Get the code of base language
     * @return string
     */
    public function baseLanguage()
    {
        return $this->baseLang;
    }

    /**
     * Determines if a given language code (or directory) is a
     * valid language inside Polyglot
     *
     * @param $code
     * @return bool
     */
    public function isLanguage($code)
    {
        if (array_key_exists($code, $this->languages)) {
            return true;
        }

        if (array_key_exists($code, $this->langRelations)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the right lang code
     * @param $lang
     * @return bool
     */
    public function getLangCode($lang)
    {
        if (array_key_exists($lang, $this->languages)) {
            return $lang;
        }

        if (array_key_exists($lang, $this->langRelations)) {
            return $this->langRelations[$lang];
        }

        return false;
    }

    /**
     * Gets the right lang code
     * @param $lang
     * @return bool
     */
    public function getLanguage($lang)
    {
        if (array_key_exists($lang, $this->languages)) {
            return $this->languages[$lang];
        }

        return '';
    }

    /**
     * Get an element Id
     *
     * @param $name
     * @param $type
     * @return bool|int
     */
    public function elementId($name, $type)
    {
        global $xoopsDB;

        if (array_key_exists($type, $this->elements) && array_key_exists($name, $this->elements[$type])) {
            return $this->elements[$type][$name];
        }

        $sql = "SELECT id_element FROM " . $xoopsDB->prefix("plugin_polyglot_elements") . " WHERE name='$name' AND type='$type' LIMIT 0, 1";
        $result = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($result) <= 0) {
            return false;
        }

        $row = $xoopsDB->fetchArray($result);
        $this->elements[$type][$name] = $row['id_element'];

        return $this->elements[$type][$name];
    }

    /**
     * Insert a new element into database to enable translations
     *
     * @param $name
     * @param $type
     * @return bool
     */
    public function registerElement($name, $type)
    {
        global $xoopsDB;

        if (array_key_exists($type, $this->elements) && array_key_exists($name, $this->elements[$type][$name])) {
            return $this->elements[$type][$name];
        }

        $sql = "INSERT INTO " . $xoopsDB->prefix("plugin_polyglot_elements") . " (`name`, `type`) VALUES ('$name', '$type')";
        if ($xoopsDB->queryF($sql)) {
            $this->elements[$type][$name] = $xoopsDB->getInsertId();
            return $this->elements[$type][$name];
        } else {
            $this->addError($xoopsDB->error());
            return false;
        }


    }
    
    public function setSystemLang($code){
        global $cuSettings;
        
        setcookie('lang', $this->getLangCode($code), time() + (3600 * 30), '/');
        $cuSettings->lang = $this->getLangCode($code);
        $this->setupLanguage($code);
        return $this->getLangCode($code);

    }

    public function setupLanguage($lang){

        if(!$this->isLanguage($lang)){
            return null;
        }

        $language = $this->getLanguage($lang);

        if(array_key_exists('rtl', $language) && 'rtl' == $language['rtl']){
            RMTemplate::getInstance()->add_attribute('html', ['dir' => 'rtl']);
        }

        if(array_key_exists('charset', $language) && 'UTF-8' != $language){
            RMTemplate::getInstance()->assign('charset', $language['charset']);
        }

    }

    static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new PolyglotCUPlugin();
        }

        return $instance;
    }
}

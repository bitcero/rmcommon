<?php
/**
 * Common Utilities Framework for Xoops
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

class Plugins
{
    private $loadedPlugins = [];

    /**
     * Checks if a plugin is already installed
     * @param $dir Plugin directory
     * @return bool
     */
    public static function isInstalled($dir)
    {

        if (isset($GLOBALS['installed_plugins'][$dir])) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Loads an installed plugin directly from file
     * @param $dir Plugin directory name
     * @return bool|object
     */
    public function load($dir)
    {

        if (array_key_exists($dir, $this->loadedPlugins)) {
            return $this->loadedPlugins[$dir];
        }

        $this->loadedPlugins[$dir] = $this->loadNew($dir);

        if (false !== $this->loadedPlugins[$dir]) {
            return $this->loadedPlugins[$dir];
        } else {
            return false;
        }

    }

    /**
     * Loads a new instance of given plugin withouth check plugins loaded caché
     * @param $dir Plugin dirname
     * @return bool|object
     */
    public function loadNew($dir)
    {
        if (!$this::isInstalled($dir)) {
            return false;
        }

        // dirnames must be in lowercase format
        $dir = strtolower($dir);

        $oldFile = RMCPATH . '/plugins/' . $dir . '/' . $dir . '-plugin.php';
        $newFile = RMCPATH . '/plugins/' . $dir . '/' . $dir . '.php';

        // Load plugin controller
        if (file_exists($oldFile)) {
            include_once $oldFile;
        } elseif(file_exists($newFile)){
            include_once $newFile;
        } else {
            return false;
        }

        $cleanDir = preg_replace("/[^A-Za-z0-9]/", '', $dir);

        $class = ucfirst($cleanDir) . 'CUPlugin';

        if (!class_exists($class))
            return false;

        $plugin = $class::getInstance();
        return $plugin;
    }

    /**
     * Load all plugins registered on database
     * @return array
     */
    public static function allInstalled()
    {

        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $result = $db->query("SELECT dir FROM " . $db->prefix("mod_rmcommon_plugins") . ' WHERE status=1');
        $plugins = array();

        while ($row = $db->fetchArray($result)) {
            $plugins[] = $row['dir'];
        }

        $plugins = \RMEvents::get()->run_event("rmcommon.installed.plugins", $plugins);

        return $plugins;

    }

    /**
     * Get settings for a specific plugin
     * @param $dir
     * @return array
     */
    public static function settings($dir){

        $settings = \RMSettings::plugin_settings($dir, true);

        return $settings;

    }


    public static function getInstance()
    {
        static $instance;

        if (isset($instance))
            return $instance;

        $instance = new Plugins();

        return $instance;
    }
}
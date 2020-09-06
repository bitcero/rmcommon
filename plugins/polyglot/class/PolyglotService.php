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

use Common\Core\Helpers\Plugins;

class PolyglotService extends \Common\Core\Helpers\ServiceAbstract implements \Common\Core\Helpers\ServiceInterface
{
    private $acceptedShorts = ['char', 'tinytext', 'varchar'];
    private $acceptedLongs = ['text', 'mediumtext', 'longtext'];

    /**
     * Translate a single var from an object
     * @param $data
     * @return mixed
     */
    public function translate($data)
    {
        global $common;
        $polyglot = Plugins::getInstance()->load('polyglot');

        if ($common->settings->lang == $polyglot->baseLanguage()) {
            return $data['original'];
        }

        /**
         * @todo Add single var translation support
         */
    }

    public function translateVars($data)
    {
        global $common;

        // Data types accepted to translate
        $accepted = [
            'char', 'tinytext', 'varchar',
            'mediumtext', 'longtext', 'text'
        ];

        $polyglot = Plugins::getInstance()->load('polyglot');

        if ($common->settings->lang == $polyglot->baseLanguage()) {
            return $data['vars'];
        }

        // Configured languages
        $languages = $polyglot->getLanguages();

        // If current language is not defined in Polyglot then exit
        if (!array_key_exists($common->settings->lang, $languages)) {
            return $data['vars'];
        }

        $db = $common->db();

        // Corresponding database tables
        $tElements = $db->prefix("plugin_polyglot_elements");
        $tShorts = $db->prefix("plugin_polyglot_" . $common->settings->lang . "_shorts");
        $tLongs = $db->prefix("plugin_polyglot_" . $common->settings->lang . "_longs");

        // Sort variables
        $shorts = [];
        $longs = [];

        foreach ($data['vars'] as $var => $value) {

            if (in_array($value['dbtype'], $this->acceptedShorts)) {
                $shorts[] = $var;
            } elseif (in_array($value['dbtype'], $this->acceptedLongs)) {
                $longs[] = $var;
            }
        }

        // Get element
        $sql = "SELECT shorts.*, longs.id_var long_id, longs.element long_element, longs.object long_object, longs.object_id object_id, longs.var long_var, longs.value long_value
                  FROM $tElements elements
                  LEFT JOIN $tShorts shorts ON (
                    elements.id_element = shorts.element
                    AND shorts.object = '$data[object]'
                    AND shorts.object_id = '$data[id]'
                  )
                  LEFT JOIN $tLongs longs ON (
                    elements.id_element = longs.element
                    AND longs.object = '$data[object]'
                    AND longs.object_id = '$data[id]'
                  )
              ";

        $result = $db->query($sql);

        while ($row = $db->fetchArray($result)) {

            if (array_key_exists($row['var'], $data['vars'])) {
                $data['vars'][$row['var']]['value'] = $row['value'];
            }

            if (array_key_exists($row['long_var'], $data['vars'])) {
                $data['vars'][$row['long_var']]['value'] = $row['long_value'];
            }

        }

        return $data['vars'];

    }

    /**
     * Singleton method
     */
    public static function getInstance()
    {
        static $instance;

        if (isset($instance))
            return $instance;

        $instance = new PolyglotService();

        return $instance;
    }

    public function saveData(array $data, $update = false)
    {
        global $common;

        if (!is_array($data) || empty($data)) {
            return 'continue';
        }

        // Check if data is valid
        if (
            empty($data['vars']) ||
            empty($data['clean']) ||
            '' == $data['element'] ||
            '' == $data['type'] ||
            '' == $data['id'] ||
            '' == $data['object']
        ) {
            return 'continue';
        }

        $plugin = Plugins::getInstance()->load('polyglot');

        /*
         * If current data is provided in base language, then let the
         * object to save in element tables
         */
        if ($plugin->baseLanguage() == $common->settings->lang) {
            return 'continue';
        }

        $languages = $plugin->getLanguages();

        /*
         * If language has not been registered in Polyglot then
         * let object to handle it
         */
        if (!array_key_exists($common->settings->lang, $languages)) {
            return 'continue';
        }

        $shorts = [];
        $longs = [];
        $element = $plugin->elementId($data['element'], $data['type']);

        if (false == $element) {
            $element = $plugin->registerElement($data['element'], $data['type']);
        }

        if (false == $element) {
            showMessage(__('Element could not be registered:', 'polyglot') . $plugin->errors(), RMMSG_ERROR);
            return false;
        }

        $db = $common->db();

        $db->queryF("DELETE FROM " . $db->prefix("plugin_polyglot_" . $common->settings->lang . "_longs") . " WHERE
                    `element` = $element AND `object`='$data[object]' AND `object_id` = $data[id]");
        $db->queryF("DELETE FROM " . $db->prefix("plugin_polyglot_" . $common->settings->lang . "_shorts") . " WHERE
                    `element` = $element AND `object`='$data[object]' AND `object_id` = $data[id]");

        $sqlLongs = "INSERT INTO " . $db->prefix("plugin_polyglot_" . $common->settings->lang . "_longs") . " (`element`,`object`,`object_id`,`var`,`value`) VALUES";
        $sqlShort = "INSERT INTO " . $db->prefix("plugin_polyglot_" . $common->settings->lang . "_shorts") . " (`element`,`object`,`object_id`,`var`,`value`) VALUES";

        $valuesShort = '';
        $valuesLongs = '';

        foreach ($data['vars'] as $var => $values) {

            if(array_key_exists('translate', $values) && $values['translate'] == 0){
                continue;
            }

            if (in_array($values['dbtype'], $this->acceptedShorts)) {
                $short = $data['clean'][$var];
                $valuesShort .= "($element, '$data[object]', $data[id], '$var', '" . $db->escape($short) . "'),";
            } elseif (in_array($values['dbtype'], $this->acceptedLongs)) {
                $long = $data['clean'][$var];
                $valuesLongs .= "($element, '$data[object]', $data[id], '$var', '" . $db->escape($long) . "'),";
            }

        }

        $valuesLongs = rtrim($valuesLongs, ",");
        $valuesShort = rtrim($valuesShort, ",");

        if('' != $valuesShort){
            $sqlShort .= $valuesShort;

            if (!$db->queryF($sqlShort)) {
                showMessage(__('Translation strings could not be save!', 'polyglot') . $db->error(), RMMSG_ERROR);
                return false;
            }
        }

        if('' != $valuesLongs){
            $sqlLongs .= $valuesLongs;

            if (!$db->queryF($sqlLongs)) {
                showMessage(__('Translation strings could not be save!', 'polyglot') . $db->error(), RMMSG_ERROR);
                return false;
            }
        }

        return true;

    }

    public function deleteData($data)
    {
        global $common;

        if (!is_array($data) || empty($data)) {
            return 'continue';
        }

        // Check if data is valid
        if (
            '' == $data['element'] ||
            '' == $data['type'] ||
            '' == $data['id'] ||
            '' == $data['object']
        ) {
            return 'continue';
        }

        $plugin = Plugins::getInstance()->load('polyglot');

        // Get all registered languages
        $languages = $plugin->getLanguages();
        $element = $plugin->elementId($data['element'], $data['type']);

        if(false == $element){
            return 'continue';
        }

        $db = $common->db();

        foreach($languages as $code => $lang){

            // Delete from longs
            $sql = "DELETE FROM " . $db->prefix("plugin_polyglot_" . $code . "_longs") . " WHERE
                    `element` = $element AND `object` = '$data[object]' AND `object_id` = $data[id]";
            $db->queryF($sql);

            // Delete from shorts
            $sql = str_replace('_longs', '_shorts', $sql);
            $db->queryF($sql);

        }

        return true;
    }
}
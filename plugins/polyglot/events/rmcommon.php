<?php
/**
 * Polyglot for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
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
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      polyglot
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

use Common\Core\Helpers\Plugins;

class PolyglotPluginRmcommonPreload
{
    /**
     * Add the namespace for PSR-4 specification
     * @param $loader
     * @return mixed
     */
    static function eventRmcommonPsr4loader($loader)
    {
        $loader->addNamespace('Plugins\Polyglot', XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/polyglot/class');
        return $loader;

    }

    /**
     * Enable multilingual support for po files
     * @param $locale
     * @return mixed
     */
    static function eventRmcommonGetLocale($locale){
        global $cuSettings;

        $polyglot = Plugins::getInstance()->load('polyglot');
        $languages = $polyglot->getLanguages();

        // Default language
        $defLang = $polyglot->settings('default');

        // Update language if required
        $lang = RMHttpRequest::get('lang', 'string', RMHttpRequest::post('lang', 'string', ''));

        // User cookie language
        $langUser = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : '';

        // Browser language
        $langBrowser = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

        if('' == $lang && '' == $langUser){

            // If $langBrowser is valid then show content using it
            if($polyglot->isLanguage($langBrowser)){
                return $polyglot->setSystemLang($langBrowser);
            } else {
                return $polyglot->setSystemLang($defLang);
            }

        }

        if($lang != '' && $polyglot->isLanguage($lang)){
            return $polyglot->setSystemLang($lang);
        }

        if($langUser != '' && $polyglot->isLanguage($langUser)){
            return $polyglot->setSystemLang($langUser);
        }

        return $locale;
    }

    /**
     * Add 'Language' option to rmcommon menu
     * @param $menu
     * @param string $dirname
     * @return mixed
     */
    static function eventRmcommonModuleMenu($menu, $dirname = '')
    {
        global $common;

        if ($dirname != 'rmcommon') {
            return $menu;
        }

        $pMenu[] = [
            'title' => __('Languages', 'polyglot'),
            'link' => '/plugins.php?p=polyglot',
            'icon' => 'svg-rmcommon-comments2 text-pink',
            'location' => 'polyglot'
        ];

        array_splice($menu, 1, 0, $pMenu);
        return $menu;

    }

    /**
     * Inserts JS support for polyglot
     * @return null
     */
    static function eventRmcommonXoopsCommonEnd()
    {
        global $common;

        load_plugin_locale('polyglot');

        $common->template()->add_style('polyglot.min.css', 'rmcommon', [
            'directory' => 'plugins/polyglot',
            'id' => 'polyglot-css'
        ]);

        $url = $common->uris()->current_url();
        if(preg_match("/plugins\.php\?p\=polyglot/", $url)){
            return null;
        }

        // Get languages from config file
        $file = XOOPS_CACHE_PATH . '/polyglot-langs.json';
        $langs = json_decode(file_get_contents($file), true);

        if(!$langs || !is_array($langs)){
            return null;
        }

        $toInsert = [];
        foreach($langs as $code => $lang){
            if($lang['status'] == 'disabled'){
                continue;
            }

            $toInsert[$code] = $lang;
        }

        $common->template()->add_inline_script('var polyglotLangs = ' . json_encode($toInsert) . ';');

        $common->template()->add_script('polyglot.min.js', 'rmcommon', [
            'directory' => 'plugins/polyglot',
            'footer' => 1,
            'id' => 'polyglot-js'
        ]);

        require $common->path('plugins/polyglot/js/polyglot-lang.php');
    }

    /**
     * Inserts 'Languages' option in Management panel in Dashboard
     * @param $tools
     * @return mixed
     */
    static function eventRmcommonGetSystemTools($tools){

        $total = count($tools);

        array_splice($tools, rand(0, $total - 2), 0, [(object) [
            'caption' => __('Languages','rmcommon'),
            'link'    => 'plugins.php?p=polyglot',
            'icon'    => 'svg-rmcommon-comments2',
            'color'   => 'pink'
        ]]);

        return $tools;
    }

    /**
     * For new RMCommon service component
     * @param array $services All added services
     * @return array
     */
    static function eventRmcommonGetServices( $services ){

        if(!Plugins::isInstalled('polyglot')){
            return $services;
        }

        $services[] = array(
            'id'            => 'polyglot', // provider id
            'name'          => 'Polyglot', // Provider name
            'description'   => __('Service provider to add multilingual support to Common Utilities and modules'),
            'service'       => 'language', // Service to provide
            'file'          => RMCPATH . '/plugins/polyglot/class/PolyglotService.php',
            'class'         => 'PolyglotService'
        );

        return $services;

    }
}
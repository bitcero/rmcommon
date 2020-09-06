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

class PolyglotPluginHeliumPreload
{
    static function eventHeliumOtherMenu($menu){
        global $common;

        $plugin = Plugins::getInstance()->load('polyglot');
        $current = $plugin->getLanguage($common->settings->lang);

        $languages = $plugin->getLanguages();

        if(empty($languages)){
            return $menu;
        }

        $link = $common->uris()->current_url();

        if(preg_match("/lang=[a-z]{2}/i", $link)){
            $link = preg_replace("/lang=([a-z]{2})/i", 'lang=[code]', $link);
        } else {
            $link .= false === stripos($link, '?') ? '?' : '&';
            $link .= 'lang=[code]';
        }

        foreach($languages as $code => $lang){

            if($lang['status'] == 'disabled'){
                continue;
            }

            $options[] = [
                'title' => $lang['name'] . ($code == $plugin->baseLanguage() ? ' (' . __('Base', 'polyglot') . ')' : ''),
                'icon' => $plugin->countryFlag($lang['country']),
                'link' => str_replace('[code]', $code, $link)
            ];
        }

        $polyglotMenu = [
            'class' => 'polyglot-menu',
            'link' => $common->url('plugins.php?p=polyglot'),
            //'icon' => $plugin->countryFlag($lang['country']),
            'title' => __('Select Language', 'polyglot'),
            'caption' => '<img src="' . $plugin->countryFlag($current['country']) . '">' . $current['name'],
            'menu' => $options
        ];

        $menu[] = $polyglotMenu;
        return $menu;

    }
}
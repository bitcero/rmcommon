<?php

/**
 * Common Utilities framework for Xoops
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
class HeliumRmcommonPreload
{
    public static function eventRmcommonAdditionalOptions($settings)
    {
        $settings['categories']['helium'] = __('Helium', 'helium');

        $af_available = RMFunctions::plugin_installed('advform');

        $settings['config'][] = [
            'name' => 'helium_logo',
            'title' => __('Logo to use', 'rmcommon'),
            'description' => __('You can specify a logo as bitmap but SVG is recommended. The logo will be resize to 29 pixels of height.', 'helium'),
            'formtype' => $af_available ? 'image-url' : 'textbox',
            'valuetype' => 'text',
            'default' => RMCPATH . '/themes/helium/images/logo-he.svg',
            'category' => 'helium',
        ];

        $settings['config'][] = [
            'name' => 'helium_xoops_metas',
            'title' => __('Render XOOPS metas?', 'rmcommon'),
            'description' => __('By enabling this option Helium will render inside &gt;head&lt; tag the XOOPS scripts, styles and metas.', 'helium'),
            'formtype' => 'yesno',
            'valuetype' => 'int',
            'default' => 0,
            'category' => 'helium',
        ];

        return $settings;
    }

    public static function eventRmcommonIncludeCommonLanguage()
    {
        define('NO_XOOPS_SCRIPTS', true);
    }

    public static function eventRmcommonPsr4loader($loader)
    {
        $loader->addNamespace('Helium', XOOPS_ROOT_PATH . '/modules/rmcommon/themes/helium/class');

        return $loader;
    }
}

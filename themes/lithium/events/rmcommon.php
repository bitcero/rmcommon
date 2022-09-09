<?php

use Common\Core\Helpers\Plugins;

class LithiumRmcommonPreload
{
    public static function eventRmcommonAdditionalOptions($settings)
    {
        $settings['categories']['lithium'] = __('Lithium Theme', 'lithium');

        $af_available = Plugins::isInstalled('advform');

        $settings['config'][] = [
            'name' => 'lithium_logo',
            'title' => __('Logo to use', 'lithium'),
            'description' => __('You can specify a logo as bitmap but SVG is recommended. The logo will be resize to 29 pixels of height.', 'lithium'),
            'formtype' => $af_available ? 'image-url' : 'textbox',
            'valuetype' => 'text',
            'default' => RMCPATH . '/themes/lithium/images/logo-he.svg',
            'category' => 'lithium',
        ];

        $settings['config'][] = [
            'name' => 'lithium_xoops_metas',
            'title' => __('Render XOOPS metas?', 'rmcommon'),
            'description' => __('By enabling this option lithium will render inside &gt;head&lt; tag the XOOPS scripts, styles and metas.', 'lithium'),
            'formtype' => 'yesno',
            'valuetype' => 'int',
            'default' => 0,
            'category' => 'lithium',
        ];

        return $settings;
    }

    public static function eventRmcommonIncludeCommonLanguage()
    {
        define('NO_XOOPS_SCRIPTS', true);
    }

    public static function eventRmcommonPsr4loader($loader)
    {
        $loader->addNamespace('Lithium', XOOPS_ROOT_PATH . '/modules/rmcommon/themes/lithium/class');

        return $loader;
    }
}

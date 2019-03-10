<?php
// $Id: rmcommon.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RmcommonRmcommonPreload
{
    public static function eventRmcommonLoadRightWidgets($widgets)
    {
        if (!defined('RMCLOCATION')) {
            return null;
        }

        require_once RMCPATH . '/include/right_widgets.php';

        global $xoopsModule;
        /*if (RMCLOCATION=='modules' && $xoopsModule->dirname()=='rmcommon' && rmc_server_var($_REQUEST, 'action', '')=='')
            $widgets[] = rmc_available_mods();

        if (RMCLOCATION=='blocks' && $xoopsModule->dirname()=='rmcommon'){
            //$widgets[] = rmc_blocks_new();
            //$widgets[] = rmc_blocks_addpos();
        }*/

        return $widgets;
    }

    public static function eventRmcommonXoopsCommonStart()
    {
    }

    public static function eventRmcommonXoopsCommonEnd()
    {
        global $xoopsConfig;

        // Get preloaders from current theme
        //RMEvents::get()->load_extra_preloads(XOOPS_THEME_PATH.'/'.$xoopsConfig['theme_set'], ucfirst($xoopsConfig['theme_set'].'Theme'));

        $url = RMUris::current_url();
        $p = parse_url($url);

        $config = RMSettings::cu_settings();

        /**
         * This event has added in order to add custom codes in a "semantic" way, but
         * the codes can be added from any pertinent place
         */
        RMEvents::get()->trigger('rmcommon.load.codes');

        if ('backend.php' == mb_substr($p['path'], -11) && $config->rss_enable) {
            require_once RMCPATH . '/rss.php';
            die();
        }
    }

    /**
     * Detects when settings has changed and if the permalink
     * feature is activated.
     *
     * @param string $dirname <p>Module dirname</p>
     * @param array $save <p>Settings options saved with values</p>
     * @param array $add <p>Settings options added with values</p>
     * @param array $delete <p>Settings options deleted from database table</p>
     * @return string
     */
    public static function eventRmcommonSavedSettings($dirname, $save, $add, $delete)
    {
        if ('rmcommon' != $dirname) {
            return $dirname;
        }

        $base = parse_url(XOOPS_URL . '/');
        $base = isset($base['path']) ? rtrim($base['path'], '/') . '/' : '/';
        $rules = 'ErrorDocument 404 ' . $base . "modules/rmcommon/404.php\n";
        foreach ($save['modules_path'] as $mod => $path) {
            $path = ltrim($path, '/');
            $rules .= "RewriteRule ^$path/?(.*)$ modules/$mod/index.php/$1 [L]\n";
            $rules .= "RewriteRule ^admin/$path/?(.*)$ modules/$mod/admin/index.php/$2 [L]\n";
        }

        if (0 == $save['permalinks']) {
            $ht = new RMHtaccess('rmcommon');
            $htResult = $ht->removeRule();
            if (true !== $htResult) {
                showMessage(
                    __('An error ocurred while trying to delete .htaccess rules!', 'rmcommon') . '<br>' .
                    __('Please delete lines starting with <code># begin rmcommon</code> and ending with <code># end rmcommon</code>', 'rmcommon'),
                    RMMSG_ERROR
                );
            }

            return $dirname;
        }

        $rules .= "RewriteRule ^rss/?(.*)$ modules/rmcommon/rss.php$1 [L]\n";

        $ht = new RMHtaccess('rmcommon');
        $htResult = $ht->write($rules);
        if (true !== $htResult) {
            showMessage(__('An error ocurred while trying to write .htaccess file!', 'rmcommon') . '<br>' .
                    __('Please try to add manually next lines:', 'rmcommon') . '<br><code>' . nl2br($rules) . '</code>', RMMSG_ERROR);
        }

        //RMSettings::write_rewrite_js( $save['modules_path'] );

        return null;
    }

    public static function eventRmcommonEditorTopPlugins($plugins, $type, $id)
    {
        global $cuIcons;

        RMTemplate::get()->add_script('cu-image-mgr.js', 'rmcommon');

        $plugins[] = '<a href="#"
                        onclick="launch_image_manager($(this));"
                        data-id="' . $id . '" data-multiple="yes"
                        data-title="' . __('Images Manager', 'rmcommon') . '"
                        data-type="' . $type . '"
                        title="' . __('Images', 'rmcommon') . '"
                        >' . $cuIcons->getIcon('svg-rmcommon-images') . '<span class="caption">' . __('Images', 'rmcommon') . '</span></a>';

        return $plugins;
    }

    public static function eventRmcommonSmartyPlugins($plugins)
    {
        $plugins['rmcommon'] = RMCPATH . '/include/smarty';

        return $plugins;
    }

    /**
     * For new RMCommon service component
     * @param array $services All added services
     * @return array
     */
    public static function eventRmcommonGetServices($services)
    {
        $services[] = [
            'id' => 'xoops-avatar', // provider id
            'name' => 'XOOPS Avatars', // Provider name
            'description' => __('Service provider to use avatars from XOOPS', 'rmcommon'),
            'service' => 'avatar', // Service to provide
            'file' => RMCPATH . '/class/AvatarService.php',
            'class' => 'XoopsAvatarService',
        ];

        $services[] = [
            'id' => 'cu-comments', // provider id
            'name' => 'Comentarios', // Provider name
            'description' => __('Comentarios de Common Utilities para XOOPS', 'rmcommon'),
            'service' => 'comments', // Service to provide
            'file' => RMCPATH . '/class/CommentsService.php',
            'class' => 'CUCommentsService',
        ];

        return $services;
    }
}

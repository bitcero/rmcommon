<?php
// $Id: core.php 1064 2012-09-17 16:46:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RmcommonCorePreload extends XoopsPreloadItem
{
    public static function eventCoreHeaderStart()
    {
        RMEvents::get()->trigger('rmcommon.core.header.start');
    }

    public static function eventCoreHeaderEnd()
    {
        /**
         * Use internal blocks manager if enabled
         */
        $config = RMSettings::cu_settings();
        if ($config->blocks_enable) {
            global $xoopsTpl;
            $bks = RMBlocksFunctions::construct_blocks();
            $bks = RMEvents::get()->trigger('rmcommon.retrieve.xoops.blocks', $bks);
            $b = &$xoopsTpl->get_template_vars('xoBlocks');
            if (is_array($bks)) {
                $blocks = array_merge($b, $bks);
            } else {
                $blocks = $b;
            }

            $xoopsTpl->assign_by_ref('xoBlocks', $blocks);
            unset($b, $bks);
        }

        RMEvents::get()->trigger('rmcommon.core.header.end');
    }

    public static function eventCoreIncludeCommonStart($args)
    {
        global $xoopsOption;
        if ('/admin.php' == mb_substr($_SERVER['REQUEST_URI'], -10) && false === mb_strpos($_SERVER['REQUEST_URI'], 'modules')) {
            header('location: ' . XOOPS_URL . '/modules/rmcommon/');
            die();
        }

        if ('system/admin.php' == mb_substr($_SERVER['REQUEST_URI'], -16) && empty($_POST)) {
            header('location: ' . XOOPS_URL . '/modules/rmcommon/');
            die();
        }

        require_once XOOPS_ROOT_PATH . '/modules/rmcommon/loader.php';
    }

    /**
     * To prevent errors when upload images with closed site
     */
    public static function eventCoreIncludeCommonLanguage()
    {
        global $xoopsConfig;

        if ('redmexico' != $xoopsConfig['cpanel']) {
            file_put_contents(XOOPS_CACHE_PATH . '/rmgui.rmc', $xoopsConfig['cpanel']);
            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $db->queryF('UPDATE ' . $db->prefix('config') . " SET conf_value='redmexico' WHERE conf_modid=0 AND conf_catid=1 AND conf_name='cpanel'");
        }

        /**
         * Check before to a rmcommon native module be installed
         */
        $fct = RMHttpRequest::get('fct', 'string', '');
        $op = RMHttpRequest::get('op', 'string', '');
        if ('modulesadmin' == $fct && 'install' == $op) {
            $dirname = RMHttpRequest::get('module', 'string', '');

            if ('' != $dirname) {
                $module = new XoopsModule();
                $module->loadInfoAsVar($dirname);
                if ($module->getInfo('rmnative')) {
                    RMUris::redirect_with_message(
                        __('Please install %s using the modules manager from Common Utilities to prevent errors during install.', 'rmcommon'),
                        RMCURL . '/modules.php?action=install&amp;dir=' . $dirname,
                        RMMSG_WARN
                    );
                }
            }
        }

        if (RMUris::current_url() == RMCURL . '/include/upload.php' && $xoopsConfig['closesite']) {
            $security = rmc_server_var($_POST, 'rmsecurity', 0);
            $data = TextCleaner::getInstance()->decrypt($security, true);
            $data = explode('|', $data); // [0] = referer, [1] = session_id(), [2] = user, [3] = token
            $xoopsUser = new XoopsUser($data[0]);
            if ($xoopsUser->isAdmin()) {
                $xoopsConfig['closesite'] = 0;
            }
        }

        RMEvents::get()->trigger('rmcommon.include.common.language');
    }

    public static function eventCoreFooterStart()
    {
        global $xoopsTpl;

        // Assign scripts and styles
        $tpl = RMTemplate::getInstance();
        $htmlScripts = $tpl->get_scripts(true);
        $htmlScripts['inlineHeader'] = $tpl->inline_scripts();
        $htmlScripts['inlineFooter'] = $tpl->inline_scripts(1);
        $htmlStyles = $tpl->get_styles(true);

        $xoopsTpl->assign('themeScripts', $htmlScripts);
        $xoopsTpl->assign('themeStyles', $htmlStyles);
        $xoopsTpl->assign('htmlAttributes', $tpl->render_attributes());

        RMEvents::get()->trigger('rmcommon.footer.start');
    }

    public static function eventCoreFooterEnd()
    {
        RMEvents::get()->trigger('rmcommon.footer.end');
        ob_end_flush();
    }

    public static function eventCoreClassTheme_blocksRetrieveBlocks($params)
    {
        global $xoopsTpl;

        $xoopsTpl->plugins_dir = RMEvents::get()->trigger('rmcommon.smarty.plugins', $xoopsTpl->plugins_dir);

        // xos_logos_PageBuilder
        $xpb = $params[0];
        // Template
        $tpl = $params[1];
        // Blocks
        $blocks = &$params[2];

        /**
         * Use internal blocks manager if enabled
         *
        $config = RMSettings::cu_settings();
        if ($config->blocks_enable) {
            global $xoopsTpl;
            $blocks = RMBlocksFunctions::construct_blocks();
            $blocks = RMEvents::get()->trigger('rmcommon.retrieve.xoops.blocks', $blocks);
            /*$b =& $xoopsTpl->get_template_vars('xoBlocks');
            if (is_array($bks)) {
                $blocks = array_merge($b, $bks);
            } else {
                $blocks = $b;
            }*
            //$xoopsTpl->assign_by_ref('xoBlocks', $blocks);

            $xpb->blocks = $blocks;

            unset($b, $bks);
        }*/

        $blocks = RMEvents::get()->trigger('rmcommon.retrieve.xoops.blocks', $blocks, $xpb, $tpl);
    }

    public static function eventCoreIncludeFunctionsRedirectheaderStart($params)
    {
        global $xoopsModule;
        global $cuSettings;

        //if ( $xoopsModule &&  !$xoopsModule->getInfo('rmnative') && $cuSettings->gui_disable )
        //    return;

        // 0 = URL
        // 1 = Time
        // 2 = Message
        // 3 = Add redirect
        // 4 = Allow external link
        RMEvents::get()->trigger('rmcommon.redirect.header', $params[0], $params[1], $params[2], $params[3], $params[4]);
        //if(!defined('XOOPS_CPFUNC_LOADED')) return;

        RMUris::redirect_with_message($params[2], $params[0], RMMSG_INFO);
        die();
    }

    public function eventRmcommonClassGuiHeader($args)
    {
        /*if (!empty($_SESSION['redirect_message'])) {
            $GLOBALS['xoTheme']->addStylesheet('xoops.css');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
            $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/plugins/jquery.jgrowl.js');
            $GLOBALS['xoTheme']->addScript('', array('type' => 'text/javascript'), '
            (function($){
            $(document).ready(function(){
                $.jGrowl("' . $_SESSION['redirect_message'] . '", {  life:3000 , position: "center", speed: "slow" });
            });
            })(jQuery);
            ');
            unset($_SESSION['redirect_message']);
        }*/
    }

    /**
     * RSS Management
     */
    public static function eventCoreIncludeCommonEnd()
    {
        global $xoopsOption;

        if (defined('RMC_CHECK_UPDATES') && 'admin' == $xoopsOption['pagetype']) {
            global $xoopsSecurity, $rmTpl;
            $rmTpl->add_head_script('var xoToken = "' . $xoopsSecurity->createToken() . '";');
        }

        // Process notifications
        $current = explode('?', RMUris::relative_url(RMUris::current_url()));
        if ('/notifications.php' == $current[0]) {
            $page = RMHttpRequest::post('page', 'string', '');
            if ('cu-notification-subscribe' == $page) {
                include RMCPATH . '/include/notifications.php';
            }

            $page = RMHttpRequest::get('page', 'string', '');
            if ('cu-notification-list' == $page) {
                include RMCPATH . '/include/notifications.php';
            }
        }

        RMEvents::get()->trigger('rmcommon.xoops.common.end');
    }

    public static function eventCoreHeaderAddmeta()
    {
        global $xoopsTpl, $xoopsConfig, $xoTheme, $rmc_config;

        if (!$xoopsTpl) {
            return;
        }

        $xoopsTpl->plugins_dir = RMEvents::get()->trigger('rmcommon.smarty.plugins', $xoopsTpl->plugins_dir);
        RMEvents::get()->trigger('rmcommon.xoops.header.add.meta');
    }

    /**
     * Next methods will add subpage to xoopsOption
     */
    public static function eventCoreIndexStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'home-page';
        RMEvents::get()->trigger('rmcommon.index.start');
    }

    public static function eventCoreEdituserStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'edit-user';
        RMEvents::get()->trigger('rmcommon.edituser.start');
    }

    public static function eventCoreReadpmsgStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'readpm';
        RMEvents::get()->trigger('rmcommon.readpm.start');
    }

    public static function eventCoreRegisterStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'register';
        RMEvents::get()->trigger('rmcommon.register.start');
    }

    public static function eventCoreUserStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'user';
        RMEvents::get()->trigger('rmcommon.user.start');
    }

    public static function eventCoreUserinfoStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'profile';
        RMEvents::get()->trigger('rmcommon.userinfo.start');
    }

    public static function eventCoreViewpmsgStart()
    {
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'pm';
        RMEvents::get()->trigger('rmcommon.viewpm.start');
    }
}

<?php
// $Id: rss.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!defined('XOOPS_MAINFILE_INCLUDED')) {
    require dirname(__DIR__) . '/../mainfile.php';
}

/**
 * Show all RSS options
 */
function show_rss_options()
{
    global $xoopsTpl, $xoopsConfig;

    include XOOPS_ROOT_PATH . '/header.php';
    $xoopsTpl->assign('xoops_pagetitle', __('RSS Center', 'rmcommon'));

    $feeds = [];
    $feeds = RMEvents::get()->run_event('rmcommon.get.feeds.list', $feeds);

    RMTemplate::get()->add_style('rss.css', 'rmcommon');
    include RMTemplate::get()->get_template('rmc-rss-center.php', 'module', 'rmcommon');

    include XOOPS_ROOT_PATH . '/footer.php';
}

function show_rss_content()
{
    global $xoopsConfig;

    require_once $GLOBALS['xoops']->path('class/template.php');
    $tpl = new XoopsTpl();
    $module = rmc_server_var($_GET, 'mod', '');

    if ('' == $module) {
        redirect_header('backend.php', 1, __('Choose an option to see its feed', 'rmcommon'));
        die();
    }

    if (!file_exists(XOOPS_ROOT_PATH . '/modules/' . $module . '/rss.php')) {
        redirect_header('backend.php', 1, __('This module does not support rss feeds', 'rmcommon'));
        die();
    }

    $GLOBALS['xoopsLogger']->activated = false;
    if (function_exists('mb_http_output')) {
        mb_http_output('pass');
    }
    header('Content-Type:text/xml; charset=utf-8');

    include XOOPS_ROOT_PATH . '/modules/' . $module . '/rss.php';

    if (!isset($rss_channel['image'])) {
        $rmc_config = RMSettings::cu_settings();
        $rss_channel['image']['url'] = $rmc_config->rssimage;
        $dimention = getimagesize(XOOPS_ROOT_PATH . '/images/logo.png');
        $rss_channel['image']['width'] = ($dimention[0] > 144) ? 144 : $dimention[0];
        $rss_channel['image']['height'] = ($dimention[1] > 400) ? 400 : $dimention[1];
    }

    include RMTemplate::get()->get_template('rmc-rss.php', 'module', 'rmcommon');
}

$action = RMHttpRequest::get('action', 'string', '');

switch ($action) {
    case 'showfeed':
        show_rss_content();
        break;
    default:
        show_rss_options();
        break;
}

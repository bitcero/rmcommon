<?php
// $Id: index.php 954 2012-05-15 03:25:53Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require_once dirname(__DIR__) . '/../include/cp_header.php';
$common->location = 'dashboard';

function construct_statistics($comments, $modules, $users)
{
  global $common;

  $widget = $common->widgets()->load('rmcommon', 'WidgetStatistics');
  $widget->setup([
    'class' => 'box-default'
  ]);
  $widget->add_title(__('Statistics', 'rmcommon'), 'lg');
  $widget->add_title(__('Your XOOPS statistics', 'rmcommon'), 'sm');

  $icons_classes = 'col-sm-6 col-md-6 col-xl-3 mb-3 mb-md-3 mb-xl-0';

  $widget->add_stat(
    value: $common->format()->quantity($comments),
    title: __('Comments', 'rmcommon'),
    icon: 'svg-lithium-comments',
    color: 'primary',
    css_classes: $icons_classes
  );

  $widget->add_stat(
    value: $common->format()->quantity($modules),
    title: __('Modules', 'rmcommon'),
    icon: 'svg-lithium-modules',
    color: 'cyan',
    css_classes: $icons_classes
  );

  $widget->add_stat(
    value: $common->format()->quantity($users),
    title: __('Users', 'rmcommon'),
    icon: 'svg-lithium-user',
    color: 'red',
    css_classes: $icons_classes
  );

  $online_handler = xoops_getHandler('online');
  $onlines = $online_handler->getAll();

  $widget->add_stat(
    value: $common->format()->quantity(count($onlines)),
    title: __('On line', 'rmcommon'),
    icon: 'svg-lithium-plug',
    color: 'green',
    css_classes: $icons_classes
  );

  return $widget;
}

function get_modules_list()
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'SELECT * FROM ' . $db->prefix('modules') . ' ORDER BY mid, weight';
    $result = $db->query($sql);
    $installed_mods = [];
    while (false !== ($row = $db->fetchArray($result))) {
        $mod = new XoopsModule();
        $mod->assignVars($row);

        $module_icon = '' != $mod->getInfo('icon48') ? XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('icon48') : '';
        $module_logo = XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('image');

        if ($mod->hasconfig()) {
            $config_link = $mod->getInfo('rmnative') ? XOOPS_URL . '/modules/rmcommon/settings.php?action=configure&amp;mod=' . $mod->mid() : XOOPS_URL . '/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod=' . $mod->mid();
        }

        $this_module = [
            'name' => $mod->getVar('name'),
            'dirname' => $mod->getVar('dirname'),
            'real_name' => $mod->getInfo('name'),
            'version' => is_array($mod->getInfo('rmversion')) ? RMModules::format_module_version($mod->getInfo('rmversion')) : $mod->getVar('version'),
            'icon' => $module_icon,
            'logo' => $module_logo,
            'admin' => $mod->getVar('hasadmin') ? XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('adminindex') : '',
            'main' => RMUris::anchor($mod->getVar('dirname')),
            'updated' => RMTimeFormatter::get()->format($mod->getVar('last_update'), __('%d% %T% %Y%', 'rmcommon')),
            'config' => isset($config_link) ? $config_link : '',
            'description' => $mod->getInfo('description'),
        ];

        $installed_mods[] = (object) $this_module;
    }

    return $installed_mods;
}

function show_dashboard()
{
    global $xoopsModule, $cuSettings, $cuIcons, $common;

    //RMFunctions::create_toolbar();

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'SELECT * FROM ' . $db->prefix('modules');
    $result = $db->query($sql);
    $installed_mods = [];
    while (false !== ($row = $db->fetchArray($result))) {
        $installed_mods[] = $row['dirname'];
    }

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
    $dirlist = XoopsLists::getModulesList();
    $available_mods = [];
    $moduleHandler = xoops_getHandler('module');

    foreach ($dirlist as $file) {
        clearstatcache();
        $file = trim($file);
        if (!in_array($file, $installed_mods, true)) {
            $module = $moduleHandler->create();
            if (!$module->loadInfo($file, false)) {
                continue;
            }
            $available_mods[] = $module;
        }
    }

    $installed_modules = get_modules_list();

    // Modules counter
    $counterModules = new Common\Widgets\Counter([
        'id' => 'counter-modules',
        'color' => 'pink',
        'icon' => 'svg-rmcommon-module',
        'class' => 'animated bounceIn',
    ]);
    $counterModules->addCell(__('Modules', 'rmcommon'), count($available_mods) + count($installed_modules));
    $counterModules->addCell(__('Installed', 'rmcommon'), count($installed_modules));
    $counterModules->addCell(__('Available', 'rmcommon'), count($available_mods));

    // Users counter
    $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('users') . ' WHERE level > 0';
    list($active) = $db->fetchRow($db->query($sql));

    $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('users') . ' WHERE level <= 0';
    list($inactive) = $db->fetchRow($db->query($sql));

    $counterUsers = new Common\Widgets\Counter([
        'id' => 'counter-users',
        'color' => 'blue',
        'icon' => 'svg-rmcommon-users2',
        'class' => 'animated bounceIn',
    ]);

    $total = $active + $inactive;

    $counterUsers->addCell(__('Users', 'rmcommon'), $total);
    $counterUsers->addCell(__('Active', 'rmcommon'), $active);
    $counterUsers->addCell(__('Inactive', 'rmcommon'), $inactive);

    $ratio = $active / ($active + $inactive);
    if ($ratio < 1) {
        $ratio = number_format($ratio, 2);
    }

    $counterUsers->addCell(__('Ratio', 'rmcommon'), $ratio * 100 . '%');

    // Comments counter
    $counterComments = new Common\Widgets\Counter([
        'id' => 'counter-comments',
        'color' => 'green',
        'icon' => 'svg-rmcommon-comments',
        'class' => 'animated bounceIn',
    ]);

    list($approved) = $db->fetchRow($db->query('SELECT COUNT(*) FROM ' . $db->prefix('mod_rmcommon_comments') . " WHERE status = 'approved'"));
    list($waiting) = $db->fetchRow($db->query('SELECT COUNT(*) FROM ' . $db->prefix('mod_rmcommon_comments') . " WHERE status != 'approved'"));
    $counterComments->addCell(__('Comments', 'rmcommon'), $approved > 0 || $waiting > 0 ? $approved + $waiting : '0');
    $counterComments->addCell(__('Approved', 'rmcommon'), $approved);
    $counterComments->addCell(__('Waiting', 'rmcommon'), $waiting);

    if ($approved <= 0 || $waiting <= 0){
        $ratio = 0;
    } else {
        $ratio = $approved / ($approved + $waiting);
    }

    if ($ratio < 1) {
        $ratio = number_format($ratio, 2);
    }

    $counterComments->addCell(__('Ratio', 'rmcommon'), $ratio * 100 . '%');

    $counterSystem = new Common\Widgets\Counter([
        'id' => 'counter-system',
        'color' => 'primary',
        'icon' => 'svg-rmcommon-rmcommon',
        'class' => 'animated bounceIn',
    ]);
    $counterSystem->addCell(__('Current Version', 'rmcommon'), RMModules::get_module_version('rmcommon', false));
    $counterSystem->addCell('XOOPS', mb_substr(str_replace('XOOPS ', '', XOOPS_VERSION), 0, 10));
    $version = explode('-', phpversion());
    $counterSystem->addCell('PHP', $version[0]);
    unset($version);

    if (method_exists($db, 'getServerVersion')) {
        $version = explode('-', $db->getServerVersion());
    } else {
        $version = '--';
    }

    $counterSystem->addCell('MySQL', $version[0]);

    if('lithium' == $common->settings->theme){
      $widget_statistics = construct_statistics(
        comments: $approved + $waiting,
        modules: count($available_mods) + count($installed_modules),
        users: $total,
      );
    }

    // Management Tools
    $managementTools[] = (object) [
        'caption' => __('Modules', 'rmcommon'),
        'link' => 'modules.php',
        'icon' => 'svg-rmcommon-module',
        'color' => 'pink',
    ];
    $managementTools[] = (object) [
        'caption' => __('Blocks', 'rmcommon'),
        'link' => 'blocks.php',
        'icon' => 'svg-rmcommon-blocks',
        'color' => 'blue',
    ];
    $managementTools[] = (object) [
        'caption' => __('Users', 'rmcommon'),
        'link' => 'users.php',
        'icon' => 'svg-rmcommon-user2',
        'color' => 'deep-orange',
    ];
    $managementTools[] = (object) [
        'caption' => __('Groups', 'rmcommon'),
        'link' => 'groups.php',
        'icon' => 'svg-rmcommon-users2',
        'color' => 'green',
    ];
    $managementTools[] = (object) [
        'caption' => __('Images', 'rmcommon'),
        'link' => 'images.php',
        'icon' => 'svg-rmcommon-images',
        'color' => 'purple',
    ];
    $managementTools[] = (object) [
        'caption' => __('Comments', 'rmcommon'),
        'link' => 'comments.php',
        'icon' => 'svg-rmcommon-comments',
        'color' => 'red',
    ];
    $managementTools[] = (object) [
        'caption' => __('Plugins', 'rmcommon'),
        'link' => 'plugins.php',
        'icon' => 'svg-rmcommon-plug',
        'color' => 'orange',
    ];
    $managementTools[] = (object) [
        'caption' => __('Updates', 'rmcommon'),
        'link' => 'updates.php',
        'icon' => 'svg-rmcommon-update',
        'color' => 'teal',
    ];

    $managementTools[] = (object) [
        'caption' => __('Preferences', 'rmcommon'),
        'link' => 'settings.php?action=configure&mod=rmcommon',
        'icon' => 'svg-rmcommon-wrench',
        'color' => 'light-blue',
    ];

    $managementTools = RMEvents::get()->trigger('rmcommon.get.system.tools', $managementTools);

    // Load recent comments
    $sql = 'SELECT * FROM ' . $db->prefix('mod_rmcommon_comments') . ' ORDER BY `posted` DESC LIMIT 0, 5';
    $result = $db->query($sql);
    $comments = [];
    while (false !== ($row = $db->fetchArray($result))) {
        $com = new RMComment();
        $com->assignVars($row);

        // Editor data
        if (!isset($ecache[$com->getVar('user')])) {
            $ecache[$com->getVar('user')] = new RMCommentUser($com->getVar('user'));
        }

        $editor = $ecache[$com->getVar('user')];

        if ($editor->getVar('xuid') > 0) {
            if (!isset($ucache[$editor->getVar('xuid')])) {
                $ucache[$editor->getVar('xuid')] = new XoopsUser($editor->getVar('xuid'));
            }

            $user = $ucache[$editor->getVar('xuid')];

            $poster = (object) [
                'id' => $user->getVar('uid'),
                'name' => $user->getVar('uname'),
                'email' => $user->getVar('email'),
                'posts' => $user->getVar('posts'),
                'avatar' => '' != $user->getVar('image') && 'blank.gif' != $user->getVar('image') ? XOOPS_UPLOAD_URL . '/' . $user->getVar('image') : RMCURL . '/images/avatar.gif',
                'rank' => $user->rank(),
            ];
        } else {
            $poster = (object) [
                'id' => 0,
                'name' => $editor->getVar('name'),
                'email' => $editor->getVar('email'),
                'posts' => 0,
                'avatar' => RMCURL . '/images/avatar.gif',
                'rank' => '',
            ];
        }

        // Get item
        $cpath = XOOPS_ROOT_PATH . '/modules/' . $row['id_obj'] . '/class/' . $row['id_obj'] . 'controller.php';

        if (is_file($cpath)) {
            if (!class_exists(ucfirst($row['id_obj']) . 'Controller')) {
                require_once $cpath;
            }

            $class = ucfirst($row['id_obj']) . 'Controller';
            $controller = new $class();
            $item = $controller->get_item($row['params'], $com);
            if (method_exists($controller, 'get_item_url')) {
                $item_url = $controller->get_item_url($row['params'], $com);
            }
        } else {
            $item = __('Unknow', 'rmcommon');
            $item_url = '';
        }

        $text = TextCleaner::getInstance()->clean_disabled_tags(
            TextCleaner::getInstance()->popuplinks(
                TextCleaner::getInstance()->nofollow(
                    TextCleaner::getInstance()->truncate($com->getVar('content'), 100)
                )
            )
        );

        $comments[] = (object) [
            'id' => $row['id_com'],
            'text' => $text,
            'poster' => $poster,
            'date' => formatTimestamp($com->getVar('posted'), 'l'),
            'ip' => $com->getVar('ip'),
            'item' => $item,
            'item_url' => $item_url,
            'module' => $row['id_obj'],
            'status' => $com->getVar('status'),
        ];
    }

    // Get dashboard widgets
    $dashboardPanels = [];
    $dashboardPanels = RMEvents::get()->trigger('rmcommon.dashboard.panels', $dashboardPanels);

    RMTemplate::getInstance()->add_body_class('dashboard');

    $common->template()->header();

    //RMTemplate::get()->add_style('dashboard.min.css', 'rmcommon');
    RMTemplate::getInstance()->add_style('pagenav.css', 'rmcommon');
    //RMTemplate::getInstance()->add_help(__('Dashboard Help','rmcommon'),'https://www.xoopsmexico.net/docs/bitcero/common-utilities/introduccion/');
    include RMTemplate::getInstance()->path('rmc-dashboard.php', 'module', 'rmcommon');

    $common->template()->footer();
}

function rm_change_theme()
{
    global $xoopsModule;

    $theme = rmc_server_var($_GET, 'theme', '');

    if (is_file(RMCPATH . '/themes/' . $theme . '/admin-gui.php')) {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'UPDATE ' . $db->prefix('config') . " SET conf_value='$theme' WHERE conf_name='theme' AND conf_modid='" . $xoopsModule->mid() . "'";
        if ($db->queryF($sql)) {
            redirectMsg('index.php', __('Theme changed successfully!', 'rmcommon'), 0);
            die();
        }
        redirectMsg('index.php', __('Theme could not be changed!', 'rmcommon') . '<br>' . $db->error(), 0);
        die();
    }

    redirectMsg('index.php', __('Specified theme does not exist!', 'rmcommon'), 1);
    die();
}

$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action) {
    case 'theme':
        rm_change_theme();
        break;
    default:
        show_dashboard();
        break;
}

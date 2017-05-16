<?php
// $Id: index.php 954 2012-05-15 03:25:53Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$common->location = 'dashboard';
include_once '../../include/cp_header.php';

function get_modules_list(){
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $sql = 'SELECT * FROM ' . $db->prefix('modules')." ORDER BY mid, weight";
    $result = $db->query($sql);
    $installed_mods = array();
    while($row = $db->fetchArray($result)){
        $mod = new XoopsModule();
        $mod->assignVars($row);

        $module_icon = $mod->getInfo('icon48') != '' ? XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('icon48') : '';
        $module_logo = XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('image');

        if ( $mod->hasconfig() )
            $config_link = $mod->getInfo( 'rmnative' ) ? XOOPS_URL . '/modules/rmcommon/settings.php?action=configure&amp;mod=' . $mod->mid() : XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$mod->mid();

        $this_module = array(
            'name' => $mod->getVar('name'),
            'dirname' => $mod->getVar('dirname'),
            'real_name' => $mod->getInfo('name'),
            'version' => is_array($mod->getInfo('rmversion')) ? RMModules::format_module_version($mod->getInfo('rmversion')) : $mod->getVar('version') / 100,
            'icon' => $module_icon,
            'logo' => $module_logo,
            'admin' => $mod->getVar('hasadmin') ? XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('adminindex') : '',
            'main' => RMUris::anchor( $mod->getVar('dirname') ),
            'updated' => RMTimeFormatter::get()->format( $mod->getVar('last_update'), __('%d% %T% %Y%', 'rmcommon')),
            'config' => isset($config_link) ? $config_link : '',
            'description' => $mod->getInfo('description')
        );

        $installed_mods[] = (object) $this_module;
    }

    return $installed_mods;
    
}

function show_dashboard(){
    global $xoopsModule, $cuSettings, $cuIcons;
    
    //RMFunctions::create_toolbar();

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'SELECT * FROM ' . $db->prefix('modules');
    $result = $db->query($sql);
    $installed_mods = array();
    while($row = $db->fetchArray($result)){
        $installed_mods[] = $row['dirname'];
    }
    
    require_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
    $dirlist = XoopsLists::getModulesList();
    $available_mods = array();
    $module_handler = xoops_gethandler('module');

    foreach ($dirlist as $file) {
        clearstatcache();
        $file = trim($file);
        if (!in_array($file, $installed_mods)) {
            $module = $module_handler->create();
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
        'color' => 'red',
        'icon' => 'svg-rmcommon-module',
        'class' => 'animated bounceIn'
    ]);
    $counterModules->addCell(__('Modules', 'rmcommon'), count($available_mods) + count($installed_modules));
    $counterModules->addCell(__('Installed', 'rmcommon'), count($installed_modules));
    $counterModules->addCell(__('Available', 'rmcommon'), count($available_mods));

    // Users counter
    $sql = "SELECT COUNT(*) FROM " . $db->prefix("users") . " WHERE level > 0";
    list($active) = $db->fetchRow($db->query($sql));

    $sql = "SELECT COUNT(*) FROM " . $db->prefix("users") . " WHERE level <= 0";
    list($inactive) = $db->fetchRow($db->query($sql));

    $counterUsers = new Common\Widgets\Counter([
        'id' => 'counter-users',
        'color' => 'blue',
        'icon' => 'svg-rmcommon-users2',
        'class' => 'animated bounceIn'
    ]);

    $total =  $active + $inactive;

    $counterUsers->addCell(__('Users', 'rmcommon'), $total);
    $counterUsers->addCell(__('Active', 'rmcommon'), $active);
    $counterUsers->addCell(__('Inactive', 'rmcommon'), $inactive);

    $ratio = $active / ($active + $inactive);
    if($ratio < 1){
        $ratio = number_format($ratio, 2);
    }

    $counterUsers->addCell(__('Ratio', 'rmcommon'), $ratio * 100 . '%');

    // Comments counter
    $counterComments = new Common\Widgets\Counter([
        'id' => 'counter-comments',
        'color' => 'green',
        'icon' => 'svg-rmcommon-comments',
        'class' => 'animated bounceIn'
    ]);

    list($approved) = $db->fetchRow($db->query("SELECT COUNT(*) FROM " . $db->prefix("mod_rmcommon_comments") . " WHERE status = 'approved'"));
    list($waiting) = $db->fetchRow($db->query("SELECT COUNT(*) FROM " . $db->prefix("mod_rmcommon_comments") . " WHERE status != 'approved'"));
    $counterComments->addCell(__('Comments', 'rmcommon'), $approved > 0 || $waiting > 0 ? $approved + $waiting : '0');
    $counterComments->addCell(__('Approved', 'rmcommon'), $approved);
    $counterComments->addCell(__('Waiting', 'rmcommon'), $waiting);

    @$ratio = $approved / ($approved + $waiting);
    if($ratio < 1){
        $ratio = number_format($ratio, 2);
    }

    $counterComments->addCell(__('Ratio', 'rmcommon'), $ratio * 100 . '%');

    $counterSystem = new Common\Widgets\Counter([
        'id' => 'counter-system',
        'color' => 'deep-orange',
        'icon' => 'svg-rmcommon-rmcommon',
        'class' => 'animated bounceIn'
    ]);
    $counterSystem->addCell(__('Current Version', 'rmcommon'), RMModules::get_module_version('rmcommon', false));
    $counterSystem->addCell('XOOPS', str_replace('XOOPS ', '', XOOPS_VERSION));
    $version = explode('-', phpversion());
    $counterSystem->addCell('PHP', $version[0]);
    unset($version);

    if(method_exists($db, 'getServerVersion')){
        $version = explode("-", $db->getServerVersion());
    } else {
        $version = '--';
    }

    $counterSystem->addCell('MySQL', $version[0]);

    // Management Tools
    $managementTools[] = (object) [
        'caption' => __('Modules','rmcommon'),
        'link'    => 'modules.php',
        'icon'    => 'svg-rmcommon-module',
        'color'   => 'pink'
    ];
    $managementTools[] = (object) [
        'caption' => __('Blocks','rmcommon'),
        'link'    => 'blocks.php',
        'icon'    => 'svg-rmcommon-blocks',
        'color'   => 'blue'
    ];
    $managementTools[] = (object) [
        'caption' => __('Users','rmcommon'),
        'link'    => 'users.php',
        'icon'    => 'svg-rmcommon-user2',
        'color'   => 'deep-orange'
    ];
    $managementTools[] = (object) [
        'caption' => __('Groups','rmcommon'),
        'link'    => 'groups.php',
        'icon'    => 'svg-rmcommon-users2',
        'color'   => 'green'
    ];
    $managementTools[] = (object) [
        'caption' => __('Images','rmcommon'),
        'link'    => 'images.php',
        'icon'    => 'svg-rmcommon-images',
        'color'   => 'purple'
    ];
    $managementTools[] = (object) [
        'caption' => __('Comments','rmcommon'),
        'link'    => 'comments.php',
        'icon'    => 'svg-rmcommon-comments',
        'color'   => 'red'
    ];
    $managementTools[] = (object) [
        'caption' => __('Plugins','rmcommon'),
        'link'    => 'plugins.php',
        'icon'    => 'svg-rmcommon-plug',
        'color'   => 'orange'
    ];
    $managementTools[] = (object) [
        'caption' => __('Updates','rmcommon'),
        'link'    => 'updates.php',
        'icon'    => 'svg-rmcommon-update',
        'color'   => 'teal'
    ];

    $managementTools[] = (object) [
        'caption' => __('Preferences','rmcommon'),
        'link'    => 'settings.php?action=configure&mod=rmcommon',
        'icon'    => 'svg-rmcommon-wrench',
        'color'   => 'light-blue'
    ];

    $managementTools = RMEvents::get()->trigger('rmcommon.get.system.tools', $managementTools);

    // Load recent comments
    $sql = "SELECT * FROM " . $db->prefix("mod_rmcommon_comments") . " ORDER BY `posted` DESC LIMIT 0, 5";
    $result = $db->query($sql);
    $comments = [];
    while($row = $db->fetchArray($result)){
        $com = new RMComment();
        $com->assignVars($row);

        // Editor data
        if(!isset($ecache[$com->getVar('user')])){
            $ecache[$com->getVar('user')] = new RMCommentUser($com->getVar('user'));
        }

        $editor = $ecache[$com->getVar('user')];

        if($editor->getVar('xuid')>0){

            if(!isset($ucache[$editor->getVar('xuid')])){
                $ucache[$editor->getVar('xuid')] = new XoopsUser($editor->getVar('xuid'));
            }

            $user = $ucache[$editor->getVar('xuid')];

            $poster = (object) array(
                'id' => $user->getVar('uid'),
                'name'  => $user->getVar('uname'),
                'email' => $user->getVar('email'),
                'posts' => $user->getVar('posts'),
                'avatar'=> $user->getVar('image')!='' && $user->getVar('image')!='blank.gif' ? XOOPS_UPLOAD_URL.'/'.$user->getVar('image') : RMCURL.'/images/avatar.gif',
                'rank'  => $user->rank(),
            );

        } else {

            $poster = (object) array(
                'id'    => 0,
                'name'  => $editor->getVar('name'),
                'email' => $editor->getVar('email'),
                'posts' => 0,
                'avatar'=> RMCURL.'/images/avatar.gif',
                'rank'  => ''
            );

        }

        // Get item
        $cpath = XOOPS_ROOT_PATH.'/modules/'.$row['id_obj'].'/class/'.$row['id_obj'].'controller.php';

        if(is_file($cpath)){
            if(!class_exists(ucfirst($row['id_obj']).'Controller'))
                include_once $cpath;

            $class = ucfirst($row['id_obj']).'Controller';
            $controller = new $class();
            $item = $controller->get_item($row['params'], $com);
            if(method_exists($controller, 'get_item_url'))
                $item_url = $controller->get_item_url($row['params'], $com);

        } else {

            $item = __('Unknow','rmcommon');
            $item_url = '';

        }

        $text = TextCleaner::getInstance()->clean_disabled_tags(
            TextCleaner::getInstance()->popuplinks(
                TextCleaner::getInstance()->nofollow(
                    TextCleaner::getInstance()->truncate($com->getVar('content'), 100)
                )
            )
        );

        $comments[] = (object) array(
            'id'        => $row['id_com'],
            'text'      => $text,
            'poster'    => $poster,
            'date'      => formatTimestamp($com->getVar('posted'), 'l'),
            'ip'        => $com->getVar('ip'),
            'item'		=> $item,
            'item_url'  => $item_url,
            'module'	=> $row['id_obj'],
            'status'	=> $com->getVar('status')
        );
    }

    // Get dashboard widgets
    $dashboardPanels = [];
    $dashboardPanels = RMEvents::get()->trigger('rmcommon.dashboard.panels', $dashboardPanels);

    RMTemplate::getInstance()->add_body_class('dashboard');

    xoops_cp_header();

    //RMTemplate::get()->add_style('dashboard.min.css', 'rmcommon');
    RMTemplate::getInstance()->add_style('pagenav.css', 'rmcommon');
    //RMTemplate::getInstance()->add_help(__('Dashboard Help','rmcommon'),'https://www.xoopsmexico.net/docs/bitcero/common-utilities/introduccion/');
    include RMTemplate::get()->path('rmc-dashboard.php', 'module', 'rmcommon');

    xoops_cp_footer();
}


function rm_change_theme(){
    global $xoopsModule;
    
    $theme = rmc_server_var($_GET,'theme','');
    
    if (is_file(RMCPATH.'/themes/'.$theme.'/admin-gui.php')){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "UPDATE ".$db->prefix("config")." SET conf_value='$theme' WHERE conf_name='theme' AND conf_modid='".$xoopsModule->mid()."'";
        if ($db->queryF($sql)){
            redirectMsg('index.php', __('Theme changed successfully!','rmcommon'), 0);
            die();
        } else {
            redirectMsg('index.php', __('Theme could not be changed!','rmcommon').'<br />'.$db->error(), 0);
            die();
        }
    }
    
    redirectMsg('index.php', __('Specified theme does not exist!','rmcommon'), 1);
    die();
    
}


$action = rmc_server_var($_REQUEST, 'action', '');

switch($action){
    case 'theme':
        rm_change_theme();
        break;
    default:
        show_dashboard();
        break;
}

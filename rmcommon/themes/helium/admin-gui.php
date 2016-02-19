<?php
/*
Theme name: Helium Theme
Theme URI: http://www.redmexico.com.mx
Version: 1.0
Author: Eduardo Cortés
Author URI: http://www.eduardocortes.mx
*/

load_theme_locale('helium', '', true);

global $xoopsUser, $xoopsSecurity, $cuIcons, $cuServices;

define('HELIUM_PATH', RMCPATH . '/themes/helium');
define('HELIUM_URL', RMCURL . '/themes/helium');

include_once HELIUM_PATH . '/class/HeliumHelper.class.php';
$xoFunc = new HeliumHelper();

// Common Utilities module menu
$mod = RMModules::load_module('rmcommon');
$rmcommon_menu = array(
    'name' => $mod->getVar('name'),
    'directory' => $mod->getVar('dirname'),
    'menu' => $xoFunc->moduleMenu('rmcommon'),
    'native' => $mod->getInfo('rmnative'),
    'rewrite' => $mod->getInfo('rewrite')
);

// System module menu
$mod = RMModules::load_module('system');
$system_menu = array(
    'name' => $mod->getVar('name'),
    'directory' => $mod->getVar('dirname'),
    'menu' => $xoFunc->moduleMenu('system'),
    'native' => $mod->getInfo('rmnative'),
    'rewrite' => $mod->getInfo('rewrite')
);

// Current Module Menu
$currentModule = array(
    'name' => $xoopsModule->getVar('name'),
    'directory' => $xoopsModule->getVar('dirname'),
    'menu' => $xoFunc->moduleMenu($xoopsModule->getVar('dirname')),
    'native' => $xoopsModule->getInfo('rmnative'),
    'rewrite' => $xoopsModule->getInfo('rewrite')
);
$currentModule = (object)$currentModule;

/**
 * Load modules and their menus
 */
$modulesList = \XoopsLists::getModulesList();
$activeModules = array();
foreach ($modulesList as $item) {
    if ($item == 'rmcommon' || $item == 'system' || $item == $xoopsModule->getVar('dirname')) {
        continue;
    }

    if (false == ($module = \XoopsModule::getByDirName($item))) {
        continue;
    }

    if (!$module->getVar('isactive')) {
        continue;
    }

    $activeModules[] = (object)array(
        'name' => $module->getVar('name'),
        'directory' => $module->getVar('dirname'),
        'menu' => $module->getAdminMenu(),
        'native' => $module->getInfo('rmnative'),
        'rewrite' => $module->getInfo('rewrite'),
        'icon' => false === $module->getInfo('icon') ? XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image') : $module->getInfo('icon')
    );

}

// Other Menus
$other_menu = RMEvents::get()->run_event('helium.other.menu');

// Left Widgets
$left_widgets = array();
$left_widgets = RMEvents::get()->run_event('rmcommon.load.left.widgets', $left_widgets);

// Right widgets
$right_widgets = array();
$right_widgets = RMEvents::get()->run_event('rmcommon.load.right.widgets', $right_widgets);

$this->add_style('bootstrap.min.css', 'helium', array('id' => 'bootstrap-css'), 'theme');
$this->add_style('rmcommon.min.css', 'helium', array(), 'theme');
$this->add_style('helium.min.css', 'helium', array(), 'theme');

/*
$color_scheme = isset($_COOKIE['color_scheme']) ? $_COOKIE['color_scheme'] : 'theme-default.css';
$this->add_style('schemes/' . $color_scheme,'helium', array('id'=>'color-scheme'), 'theme');
unset($color_scheme);
*/

$this->add_style('font-awesome.min.css', 'rmcommon', array('footer' => 1));
$this->add_style('icomoon.min.css', 'rmcommon', array('footer' => 1));
$this->add_style('jquery.window.css', 'helium', array('footer' => 1), 'theme');
$this->add_script('bootstrap.min.js', 'helium', array('footer' => 1, 'id' => 'bootstrap-js'), 'theme');
$this->add_script('jquery.ck.js', 'rmcommon', array('footer' >= 1));
$this->add_script('helium.min.js', 'helium', array('footer' => 1, 'id' => 'helium-js'), 'theme');
$this->add_script('updates.js', 'rmcommon', array('footer' => 1));

// Delete unused scripts and styles
$content = preg_replace("/<script.*" . str_replace("/", '\/', XOOPS_URL) . "\/js\/.*/", '', $content);
$content = preg_replace("/<link.*" . str_replace("/", '\/', XOOPS_URL) . "\/css\/.*\/>/", '', $content);

// Unset certain scripts
RMTemplate::getInstance()->clear_styles('rmcommongeneralmincss');
RMTemplate::getInstance()->clear_styles('rmcommonpagenavcss');
RMTemplate::getInstance()->clear_styles('cu-blocks-css');

$tp6Alerts = array(
    RMMSG_ERROR => 'alert-danger',
    RMMSG_INFO => 'alert-info',
    RMMSG_OTHER => 'alert-info',
    RMMSG_SAVED => 'alert-success',
    RMMSG_SUCCESS => 'alert-success',
    RMMSG_WARN => 'alert-warning'
);

$this->add_head_script("helium_url = '" . HELIUM_URL . "';");
$this->add_head_script("xoUrl = '" . XOOPS_URL . "';");

// Has main?
if ($xoopsModule->hasmain()) {
    $mainLink = XOOPS_URL . '/modules/' . $xoopsModule->dirname();
    if (is_file(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/' . $xoopsModule->dirname() . 'controller.php')) {
        include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/' . $xoopsModule->dirname() . 'controller.php';
        $class = ucfirst($xoopsModule->dirname()) . 'Controller';
        if (class_exists($class)) {
            $controller = new $class();
            $mainLink = $controller->get_main_link();
        }
    }
}

// JS Language
include RMCPATH . '/js/cu-js-language.php';

!defined('RMCLOCATION') ? define('RMCLOCATION', '') : true;
!defined('RMCSUBLOCATION') ? define('RMCSUBLOCATION', '') : true;

// Scripts
$heliumScripts = \RMTemplate::get()->get_scripts(true);
$heliumStyles = \RMTemplate::get()->get_styles(true);

// User Rank
$userRank = $xoopsUser->rank();

// Help
$helpLinks = RMTemplate::getInstance()->help();

// Body classess
if ( !array_key_exists('sidebar', $_COOKIE) || $_COOKIE['sidebar'] == 'visible' ){
    RMTemplate::getInstance()->add_attribute('html', ['class' => 'sidebar']);
}
if (RMBreadCrumb::get()->count() > 0) {
    RMTemplate::getInstance()->add_attribute('html', ['class' => 'with-breadcrumb']);
}

RMTemplate::getInstance()->add_attribute('html', [
    'class' => RMTemplate::getInstance()->body_classes()
]);

// The logo
$logoHelium = trim($cuSettings->helium_logo);
if ('' == $logoHelium){
    $logoHelium = HELIUM_URL . '/images/logo-he.svg';
}

if(substr($logoHelium, -4) == '.svg'){
    $logoHelium = file_get_contents($logoHelium);
} else {
    $logoHelium = '<img src="' . $logoHelium . '">';
}

// Xoops Metas
$showXoopsMetas = $cuSettings->helium_xoops_metas;

// Display theme
include_once HELIUM_PATH . '/theme.php';

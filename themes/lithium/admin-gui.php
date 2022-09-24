<?php
/*
Theme name: Lithium Theme
Theme URI: https://bitcero.dev
Version: 1.0
Author: Eduardo Cortés
Author URI: http://www.eduardocortes.mx
*/

global $common;

load_theme_locale('lithium', '', true);

global $xoopsUser, $xoopsSecurity, $cuIcons, $cuServices;

const LITHIUM_PATH = RMCPATH . '/themes/lithium';
const LITHIUM_URL = RMCURL . '/themes/lithium';

require_once LITHIUM_PATH . '/class/LithiumHelper.class.php';
$xoFunc = new LithiumHelper();

// Common Utilities module menu
$mod = RMModules::load_module('rmcommon');
$rmcommon_menu = [
    'name' => $mod->getVar('name'),
    'directory' => $mod->getVar('dirname'),
    'menu' => $xoFunc->moduleMenu('rmcommon'),
    'native' => $mod->getInfo('rmnative'),
    'rewrite' => $mod->getInfo('rewrite'),
];

// System module menu
$mod = RMModules::load_module('system');
$system_menu = [
    'name' => $mod->getVar('name'),
    'directory' => $mod->getVar('dirname'),
    'menu' => $xoFunc->moduleMenu('system'),
    'native' => $mod->getInfo('rmnative'),
    'rewrite' => $mod->getInfo('rewrite'),
];

// Current Module Menu
if ('rmcommon' != $xoopsModule->getVar('dirname')) {
    $currentModule = [
        'name' => $xoopsModule->getVar('name'),
        'directory' => $xoopsModule->getVar('dirname'),
        'menu' => $xoFunc->moduleMenu($xoopsModule->getVar('dirname')),
        'native' => $xoopsModule->getInfo('rmnative'),
        'rewrite' => $xoopsModule->getInfo('rewrite'),
    ];
    $currentModule = (object)$currentModule;
}

/**
 * Load modules and their menus
 */
$modulesList = \XoopsLists::getModulesList();
$activeModules = [];
foreach ($modulesList as $item) {
    if ('rmcommon' == $item || 'system' == $item || $item == $xoopsModule->getVar('dirname')) {
        continue;
    }

    if (false === ($module = \XoopsModule::getByDirname($item))) {
        continue;
    }

    if (!$module->getVar('isactive')) {
        continue;
    }

    $activeModules[] = (object)[
        'name' => $module->getVar('name'),
        'directory' => $module->getVar('dirname'),
        'menu' => $module->getAdminMenu(),
        'native' => $module->getInfo('rmnative'),
        'rewrite' => $module->getInfo('rewrite'),
        'icon' => false === $module->getInfo('icon') ? XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image') : $module->getInfo('icon'),
    ];
}

// Other Menus
$other_menu = [];
$other_menu = $common->events()->trigger('lithium.other.menu', $other_menu);

// Left Widgets
$left_widgets = [];
$left_widgets = $common->events()->trigger('rmcommon.load.left.widgets', $left_widgets);

// Right widgets
$right_widgets = [];
$right_widgets = $common->events()->trigger('rmcommon.load.right.widgets', $right_widgets);

$this->add_style('bootstrap.min.css', 'lithium', ['id' => 'bootstrap-css'], 'theme');
$this->add_style('rmcommon.min.css', 'lithium', ['id' => 'rmcommon-css'], 'theme');
$this->add_style('lithium.min.css', 'lithium', ['id' => 'lithium-css'], 'theme');

/*
$color_scheme = isset($_COOKIE['color_scheme']) ? $_COOKIE['color_scheme'] : 'theme-default.css';
$this->add_style('schemes/' . $color_scheme,'lithium', array('id'=>'color-scheme'), 'theme');
unset($color_scheme);
*/

$this->add_jquery(false, false);
$this->add_style('font-awesome.min.css', 'rmcommon', ['footer' => 1]);
$this->add_style('icomoon.min.css', 'rmcommon', ['footer' => 1]);
$this->add_style('jquery.window.css', 'lithium', ['footer' => 1], 'theme');
$this->add_script('bootstrap.min.js', 'lithium', ['footer' => 1, 'id' => 'bootstrap-js'], 'theme');
$this->add_script('jquery.ck.js', 'rmcommon', ['footer' >= 1]);
$this->add_script('lithium.min.js', 'lithium', ['footer' => 1, 'id' => 'lithium-js'], 'theme');
$this->add_script('updates.js', 'rmcommon', ['footer' => 1]);

// Delete unused scripts and styles
$content = preg_replace('/<script.*' . str_replace('/', '\/', XOOPS_URL) . "\/js\/.*/", '', $content);
$content = preg_replace('/<link.*' . str_replace('/', '\/', XOOPS_URL) . "\/css\/.*\>/", '', $content);

// Unset certain scripts
RMTemplate::getInstance()->clear_styles('rmcommongeneralmincss');
RMTemplate::getInstance()->clear_styles('rmcommonpagenavcss');
RMTemplate::getInstance()->clear_styles('cu-blocks-css');

$tp6Alerts = [
    RMMSG_ERROR => 'alert-danger',
    RMMSG_INFO => 'alert-info',
    RMMSG_OTHER => 'alert-info',
    RMMSG_SAVED => 'alert-success',
    RMMSG_SUCCESS => 'alert-success',
    RMMSG_WARN => 'alert-warning',
];

$this->add_head_script("lithium_url = '" . LITHIUM_URL . "';");
$this->add_head_script("xoUrl = '" . XOOPS_URL . "';");

// Has main?
if ($xoopsModule->hasmain()) {
    $mainLink = XOOPS_URL . '/modules/' . $xoopsModule->dirname();
    if (is_file(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/' . $xoopsModule->dirname() . 'controller.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/' . $xoopsModule->dirname() . 'controller.php';
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
$lithiumScripts = \RMTemplate::get()->get_scripts(true);
$lithiumStyles = \RMTemplate::get()->get_styles(true);

// User Rank
$userRank = $xoopsUser->rank();

// Help
$helpLinks = RMTemplate::getInstance()->help();

// Body classess
if (!array_key_exists('sidebar', $_COOKIE) || 'visible' == $_COOKIE['sidebar']) {
    RMTemplate::getInstance()->add_attribute('html', ['class' => 'sidebar']);
}
if (RMBreadCrumb::get()->count() > 0) {
    RMTemplate::getInstance()->add_attribute('html', ['class' => 'with-breadcrumb']);
}

if (count(RMTemplate::getInstance()->get_toolbar()) > 0) {
    RMTemplate::getInstance()->add_body_class('with-toolbar');
}

RMTemplate::getInstance()->add_attribute('body', [
    'class' => RMTemplate::getInstance()->body_classes(),
]);

$common->template()->add_attribute('body', [
    'class' => $xoopsModule->dirname(),
]);

// The logo
$lithium_logo_icon = isset($cuSettings->lithium_logo_icon) ? trim($cuSettings->lithium_logo_icon) : '';
$lithium_logo_text = isset($cuSettings->lithium_logo_text) ? trim($cuSettings->lithium_logo_text) : 'Utilities';
if ('' == $lithium_logo_icon) {
  $lithium_logo_icon = LITHIUM_URL . '/assets/icons/lithium.svg';
}

if ('.svg' == mb_substr($lithium_logo_icon, -4)) {
  $lithium_logo_icon = file_get_contents($lithium_logo_icon);
} else {
  $lithium_logo_icon = '<img src="' . $lithium_logo_icon . '">';
}

// Xoops Metas
$showXoopsMetas = isset($cuSettings->lithium_xoops_metas) ? $cuSettings->lithium_xoops_metas : false;

/*
Load customized user menu, generated by modules or plugins.
The menu should be provided as an array with the following structure:

[
  'caption' => 'Menu caption',
  'link' => 'Link to the menu',
  'icon' => 'Icon for the menu',
]
*/
$lithium_user_menu = [];
$lithium_user_menu = $common->events()->trigger('lithium.user.menu', $lithium_user_menu);

// Display theme
require_once LITHIUM_PATH . '/theme.php';

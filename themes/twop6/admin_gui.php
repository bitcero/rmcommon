<?php
/*
Theme name: Two Point Six
Theme URI: http://www.redmexico.com.mx
Version: 1.0
Author: bitcero
Author URI: http://www.bitcero.info
*/

load_theme_locale('twop6','xoops.css',true);

global $xoopsUser, $xoopsSecurity;

define('TWOP6_PATH', RMCPATH.'/themes/twop6');
define('TWOP6_URL', RMCURL.'/themes/twop6');

include_once TWOP6_PATH.'/class/twop6functions.class.php';
$xoFunc = new Twop6Functions();

// Cookies
//RMTemplate::get()->add_local_script('jquery.ck.js', 'rmcommon','include');

// Get current module menu
if($xoopsModule->dirname()!='rmcommon' && $xoopsModule->dirname()!='system')
    $xoFunc->currentModuleMenu();

// Common Utilities module menu
$rmcommon_menu = $xoFunc->moduleMenu('rmcommon');

// System module menu
$system_menu = $xoFunc->moduleMenu('system');
    
// Other Menus
$other_menu = RMEvents::get()->run_event('twop6.other.menu');

// Left Widgets
$left_widgets = array();
$left_widgets = RMEvents::get()->run_event('rmcommon.load.left.widgets', $left_widgets);

// Right widgets
$right_widgets = array();
$right_widgets = RMEvents::get()->run_event('rmcommon.load.right.widgets', $right_widgets);

$tp6Span = 12;
if($left_widgets)
    $tp6Span -= 2;

if($right_widgets)
    $tp6Span -= 3;

$this->add_style('bootstrap.min.css','rmcommon');
$this->add_style('theme-default.css','twop6', array(), 'theme');
$this->add_style('general.css','rmcommon', array());
$this->add_style('2.6.css','twop6', array(), 'theme');
$this->add_style('font-awesome.min.css','rmcommon', array('footer' => 1));
$this->add_style('icomoon.css','rmcommon', array('footer' => 1));
$this->add_style('jquery.window.css','twop6', array('footer' => 1), 'theme');
$this->add_script( 'bootstrap.js', 'rmcommon' );
$this->add_script('2.6.js', 'twop6', array('footer' => 1), 'theme');
$this->add_script('jquery.window.min.js', 'twop6', array('footer' => 1), 'theme');
$this->add_script('updates.js', 'rmcommon', array('footer' => 1));
$this->add_script('jquery.debounce.min.js', 'rmcommon', array('footer' => 1));

// Delete unused scripts and styles
$content = preg_replace("/<script.*".str_replace("/",'\/', XOOPS_URL)."\/js\/.*/",'', $content);
$content = preg_replace("/<link.*".str_replace("/",'\/', XOOPS_URL)."\/css\/.*\/>/",'', $content);

$tp6Alerts = array(
    RMMSG_ERROR => 'alert-danger',
    RMMSG_INFO => 'alert-info',
    RMMSG_OTHER => '',
    RMMSG_SAVED => 'alert-success',
    RMMSG_SUCCESS => 'alert-success',
    RMMSG_WARN => 'alert-warning'
);

$rmc_messages = array();
if (isset($_SESSION['cu_redirect_messages'])){
    foreach ($_SESSION['cu_redirect_messages'] as $msg){
        $rmc_messages[] = $msg;
    }
    unset($_SESSION['cu_redirect_messages']);
}

$this->add_head_script("twop6_url = '".TWOP6_URL."';");
$this->add_head_script("xoUrl = '".XOOPS_URL."';");

$xoModules = include('include/modules.php');

// Has main?
if($xoopsModule->hasmain()){
    $mainLink = XOOPS_URL.'/modules/'.$xoopsModule->dirname();
    if(is_file(XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->dirname().'/class/'.$xoopsModule->dirname().'controller.php')){
        include_once XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->dirname().'/class/'.$xoopsModule->dirname().'controller.php';
        $class = ucfirst($xoopsModule->dirname()).'Controller';
        if(class_exists($class)){
            $controller = new $class();
            $mainLink = $controller->get_main_link();
        }
    }
}

// JS Language
include RMCPATH . '/js/cu-js-language.php';

// Display theme
include_once TWOP6_PATH.'/theme.php';

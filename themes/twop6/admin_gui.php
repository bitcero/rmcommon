<?php
/*
Theme name: Two Point Six
Theme URI: http://www.redmexico.com.mx
Version: 1.0
Author: bitcero
Author URI: http://www.bitcero.info
*/

load_theme_locale('twop6','',true);

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
$this->add_theme_style('2.6.css','twop6');
$this->add_style('font-awesome.min.css','rmcommon');
$this->add_theme_style('jquery.window.css','twop6');
$this->add_script( 'bootstrap.min.js', 'rmcommon', array('directory' => 'include') );
$this->add_theme_script('2.6.js', 'twop6');
$this->add_theme_script('jquery.window.min.js', 'twop6');

// Delete unused scripts and styles
$content = preg_replace("/<script.*".str_replace("/",'\/', XOOPS_URL)."\/js\/.*/",'', $content);
$content = preg_replace("/<link.*".str_replace("/",'\/', XOOPS_URL)."\/css\/.*\/>/",'', $content);

$tp6Alerts = array(
    RMMSG_ERROR => 'alert-error',
    RMMSG_INFO => 'alert-info',
    RMMSG_OTHER => '',
    RMMSG_SAVED => 'alert-success',
    RMMSG_SUCCESS => 'alert-success',
    RMMSG_WARN => 'alert-block'
);

$this->add_head_script("twop6_url = '".TWOP6_URL."';");
$this->add_head_script("xoUrl = '".XOOPS_URL."';");

$xoModules = include('include/modules.php');

$sUrls = '';
$sNames = '';
foreach($xoModules as $mod){
    $sUrls .= $sUrls=='' ? '"'.$mod['admin_link'].'"' : ',"'.$mod['admin_link'].'"';
    $sNames .= $sNames=='' ? '"'.$mod['name'].'"' : ',"'.$mod['name'].'"';
}

// rmcommon pages
$sUrls .= ',
          "'.XOOPS_URL.'/modules/rmcommon/users.php",
          "'.XOOPS_URL.'/modules/rmcommon/comments.php",
          "'.XOOPS_URL.'/modules/rmcommon/images.php",
          "'.XOOPS_URL.'/modules/rmcommon/modules.php",
          "'.XOOPS_URL.'/modules/rmcommon/blocks.php",
          "'.XOOPS_URL.'/modules/rmcommon/plugins.php"';
          
$sNames .= ',
           "'.__('Users management','twop6').'",
           "'.__('Comments management','twop6').'",
           "'.__('Images manager','twop6').'",
           "'.__('Modules manager','twop6').'",
           "'.__('Blocks manager','twop6').'",
           "'.__('Plugins manager','twop6').'"';

// system preferences
$sUrls .= ',"'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=1",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=2",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=3",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=4",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=5",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=6",
          "'.XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id=7"';
$sNames .= ',"'.__('General preferences','twop6').'",
           "'.__('Users settings','twop6').'",
           "'.__('Meta tags and footer','twop6').'",
           "'.__('Word censor','twop6').'",
           "'.__('Search options','twop6').'",
           "'.__('Email settings','twop6').'",
           "'.__('Authentication options','twop6').'"';

global $rmEvents;
$sData = $rmEvents->run_event('twop6.get.typeahead', array('names' => $sNames, 'urls' => $sUrls));

$this->add_head_script('var sObject = {urls: ['.$sData['urls'].'], names: ['.$sData['names'].']};');

unset($sUrls, $sNames);

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

// Display theme
include_once TWOP6_PATH.'/theme.php';

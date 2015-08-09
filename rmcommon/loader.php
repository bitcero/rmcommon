<?php
// $Id: loader.php 1041 2012-09-09 06:23:26Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * rmcommon constants
 */
if ( !defined( 'RMCPATH' ) )
    define("RMCPATH",XOOPS_ROOT_PATH.'/modules/rmcommon');
if ( !defined( 'RMCURL' ) )
    define("RMCURL",XOOPS_URL.'/modules/rmcommon');
define('ABSURL', XOOPS_URL);
define('ABSPATH', XOOPS_ROOT_PATH);
define('RMCVERSION','2.2.9.6');

/**
 * Welcome Screen
 */
if ( isset( $_COOKIE['rmcwelcome'] ) ){
    $domain = preg_replace("/http:\/\/|https:\/\//", '', XOOPS_URL);
    setcookie( "rmcwelcome", 1, time() - 3600, '/', $domain );
    header('location: ' . RMCURL . '/about.php' );
    die();
}

/**
 * Messages Levels
 */
define('RMMSG_INFO', 0);
define('RMMSG_WARN', 1);
define('RMMSG_SUCCESS', 2);
define('RMMSG_SAVED', 3);
define('RMMSG_ERROR', 4);
define('RMMSG_OTHER', 5);

ob_start('cu_render_output');

/**
* This file contains the autoloader function files from RMCommon Utilities
*/
function rmc_autoloader($class){
	global $xoopsModule;

	if(class_exists($class)) return;

    /**
     * New autoloader method
     * $class = new Module_ClassName();
     * The class name must contain the module directory name separated with a "_"
     * from the file name.
     * Common Utilities will search for "PATH/module/classname.class.php" file
     */
    $data = explode("_", strtolower($class));
    
    if(count($data) >= 2){

        if ( 'editor' == $data[0]){

            $file = RMCPATH . '/api/editors/' . $data[1] . '/' . strtolower( $data[1] ) . '.php';
            if ( file_exists( $file )) {
                require $file;
                return null;
            }

        } elseif ( is_dir( XOOPS_ROOT_PATH . '/modules/' . $data[0] ) ){

            // Module exists! Then will search for /{dir}/{class}.class.php
            $name = substr(strtolower($class), strlen($data[0]) + 1);
            $file = XOOPS_ROOT_PATH . '/modules/' . $data[0] . '/class/' . strtolower( str_replace( '_', '-', $name) ) . '.class.php';
            if( is_file($file) ){
                require $file;
                return;
            }

            // Helpers from rmcommon have a different name structure
            if ( 'rmcommon' == $data[0] ){
                $file = XOOPS_ROOT_PATH . '/modules/rmcommon/class/helpers/' . strtolower(str_replace("_", ".", $class) ) . '.class.php';
                if( is_file($file) ){
                    require $file;
                    return;
                }
            }

        }

    }

    /**
     * Old method maintained for backward compatibility
     */
    $class = str_replace("\\", "/", $class);
	
	$class = strtolower($class);
	
	if($class=='xoopskernel') return;
	
	if (substr($class, 0, 2)=='rm') $class = substr($class, 2);

	if (substr($class, strlen($class) - strlen('handler'))=='handler'){
		$class = substr($class, 0, strlen($class) - 7);
	}

    $class = str_replace("_", "-", $class);
      
    $paths = array(
    	'/api',
        '/class',
        '/class/ar',
        '/class/helpers',
        '/class/modules',
        '/class/fields',
        '/class/form',
        '/kernel',
    );

    if (is_a($xoopsModule, 'XoopsModule') && $xoopsModule->dirname()!='system'){
    	$paths[] = '/modules/'.$xoopsModule->dirname().'/class';
    }

    foreach ($paths as $path){

    	if ( file_exists( RMCPATH.$path.'/'.$class.'.class.php' ) ){
            include_once RMCPATH.$path.'/'.$class.'.class.php';
            break;
        } elseif ( file_exists( RMCPATH.$path.'/'.$class.'.php' ) ){
            include_once RMCPATH.$path.'/'.$class.'.php';
            break;
        } elseif ( file_exists( RMCPATH.$path.'/'.$class.'.trait.php' ) ){
            include_once RMCPATH.$path.'/'.$class.'.trait.php';
            break;
        } elseif ( file_exists( XOOPS_ROOT_PATH.$path.'/'.$class.'.php' ) ){
        	include_once XOOPS_ROOT_PATH.$path.'/'.$class.'.php';
            break;
        } elseif ( file_exists( XOOPS_ROOT_PATH.$path.'/'.$class.'.class.php' ) ){
            include_once XOOPS_ROOT_PATH.$path.'/'.$class.'.class.php';
            break;
        }

    }
	
}

spl_autoload_register('rmc_autoloader');

/**
* Modify the page output to include some new features
* 
* @param mixed $output
* @return string
*/
function cu_render_output($output){
	global $xoTheme, $xoopsTpl;
    
    $rmEvents = RMEvents::get();
    
    if (function_exists('xoops_cp_header')) return $output;    
    
    $page = $output;
    if($xoopsTpl){
        if(defined('COMMENTS_INCLUDED') && COMMENTS_INCLUDED){
            RMTemplate::get()->add_style('comments.css', 'rmcommon');
        }
    }
    
    include_once RMTemplate::get()->get_template('rmc-header.php', 'module', 'rmcommon');
    $rtn .= $scripts;
    $rtn .= $styles;
    $rtn .= $heads;
    
    $find = array();
    $repl = array();
    foreach($metas as $name => $content){
        
        $str = "<meta\s+name=['\"]??".$name."['\"]??\s+content=['\"]??(.+)['\"]??\s*\/?>";
        if(preg_match($str, $page)){
            $find[] = $str;
            $str = "meta name=\"$name\" content=\"$content\" />\n";
            $repl[] = $str;
        } else {
            
            $rtn .= "\n<meta name=\"$name\" content=\"$content\" />";
            
        }
        
    }
    
    if(!empty($find))
        $page = preg_replace($find, $repl, $page);
    
    $pos = strpos($page, "</body>");
    if($pos===FALSE) return $output;
        
    $ret = substr($page, 0, $pos)."\n";
    $ret .= $fscripts."\n".$fstyles;
    $ret .= substr($page, $pos);
    
    $page = $ret;
    
    $pos = strpos($page, "<!-- RMTemplateHeader -->");
    if($pos!==FALSE){
        $page = str_replace('<!-- RMTemplateHeader -->', $rtn, $page);
        $page = $rmEvents->run_event('rmcommon.end.flush',$page);
        return $page;
    }
    
    $pos = strpos($page, "</head>");
    if($pos===FALSE) return $output;
        
    $ret = substr($page, 0, $pos)."\n";
    $ret .= $rtn;
    $ret .= substr($page, $pos);
    
    $ret = $rmEvents->run_event('rmcommon.end.flush',$ret);
    
    return $ret;
}

include_once XOOPS_ROOT_PATH.'/class/logger/xoopslogger.php';
include_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';

$dbF = new XoopsDatabaseFactory();
$db =& $dbF->getDatabaseConnection();

$GLOBALS['rmFunctions'] = new RMFunctions();
global $rmFunctions;

/**
 * New object to manage Common Utilities configurations
 */
$GLOBALS['cuSettings'] = (object) $rmFunctions->settings->cu_settings();
global $cuSettings;

/**
 * Do not use $rmc_config, use $cuSettings instead
 * @Todo: Delete references to $rmc_config
 * @deprecated
 */
$GLOBALS['rmc_config'] = (array) $cuSettings;
global $rmc_config;


// Base classes
$GLOBALS['rmEvents'] = RMEvents::get();
$GLOBALS['rmTpl'] = RMTemplate::get();
$GLOBALS['rmCodes'] = RMCustomCode::get();

global $rmEvents, $rmTpl, $rmCodes;

define('RMCLANG', $rmEvents->run_event('rmcommon.set.language', $cuSettings->lang));

// Load plugins
$file = XOOPS_CACHE_PATH.'/plgs.cnf';
$plugins = array();
$GLOBALS['installed_plugins'] = array();

if (file_exists($file)){
    $plugins = json_decode(file_get_contents($file), true);
}

if (empty($plugins) || !is_array($plugins)){

    $result = $db->query("SELECT dir FROM ".$db->prefix("mod_rmcommon_plugins").' WHERE status=1');
    while($row = $db->fetchArray($result)){
    	$GLOBALS['installed_plugins'][$row['dir']] = true;
        $plugins[] = $row['dir'];
        $rmEvents->load_extra_preloads(RMCPATH.'/plugins/'.$row['dir'], ucfirst($row['dir']).'Plugin');
    }
    file_put_contents($file, json_encode($plugins));

} else {

    foreach($plugins as $p){
        $GLOBALS['installed_plugins'][$p] = true;
        $rmEvents->load_extra_preloads(RMCPATH.'/plugins/'.$p, ucfirst($p).'Plugin');
    }

}

// Load GUI theme events
$rmEvents->load_extra_preloads(RMCPATH.'/themes/'.$cuSettings->theme, ucfirst($cuSettings->theme));

unset($plugins);
unset($file);

$GLOBALS['installed_plugins'] = $rmEvents->run_event("rmcommon.plugins.loaded", $GLOBALS['installed_plugins']);

require_once 'api/l10n.php';

// Load rmcommon language
load_mod_locale('rmcommon');

if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->dirname()!='rmcommon')
    load_mod_locale($xoopsModule->dirname());

if (!$cuSettings){
    _e('Sorry, Red Mexico Common Utilities has not been installed yet!');
    die();
}

$rmEvents->run_event('rmcommon.base.loaded');

$rmTpl->add_head_script('var xoUrl = "'.XOOPS_URL.'";');

if($cuSettings->updates && isset( $xoopsOption['pagetype'] ) && $xoopsOption['pagetype']=='admin'){

    $interval = $cuSettings->updatesinterval <= 0 ? 7 : $cuSettings->updatesinterval;
    if(file_exists(XOOPS_CACHE_PATH.'/updates.chk'))
        $updates = unserialize(base64_decode(file_get_contents(XOOPS_CACHE_PATH.'/updates.chk')));
    else
        $updates = array('date'=>0,'total'=>0,'updates'=>array());

    $rmTpl->add_script('updates.js','rmcommon', array('directory' => 'include', 'footer' => 1));
    
    if( $updates['date'] < ( time()-( $cuSettings->updatesinterval * 86400 ) ) ){
        $rmTpl->add_head_script('(function(){rmCheckUpdates();})();');
        define('RMC_CHECK_UPDATES', 1);
    }else{
        $rmTpl->add_head_script('$(document).ready(function(){rmCallNotifier('.$updates['total'].');});');
    }
    
}

/**
 * Add ajax controller script
 */
if ( defined("XOOPS_CPFUNC_LOADED") || ( isset($xoopsOption) && array_key_exists( 'pagetype', $xoopsOption ) && $xoopsOption['pagetype'] == 'admin' ) ){
    $rmTpl->add_script( 'cu-settings.php', 'rmcommon', array('footer' => 1) );
    $rmTpl->add_script( 'jquery.validate.min.js', 'rmcommon', array('footer' => 1) );
}

include_once RMCPATH.'/include/tpl_functions.php';

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
if (!defined('RMCPATH')) {
  define('RMCPATH', XOOPS_ROOT_PATH . '/modules/rmcommon');
}
if (!defined('RMCURL')) {
  define('RMCURL', XOOPS_URL . '/modules/rmcommon');
}
const ABSURL = XOOPS_URL;
const ABSPATH = XOOPS_ROOT_PATH;
const RMCVERSION = '2.3.7.5';

/**
 * Welcome Screen
 */
if (isset($_COOKIE['rmcwelcome'])) {
  $domain = preg_replace("/http:\/\/|https:\/\//", '', XOOPS_URL);
  setcookie('rmcwelcome', 1, time() - 3600, '/', $domain);
  header('location: ' . RMCURL . '/about.php');
  die();
}

/**
 * Messages Levels
 */
const RMMSG_INFO = 0;
const RMMSG_WARN = 1;
const RMMSG_SUCCESS = 2;
const RMMSG_SAVED = 3;
const RMMSG_ERROR = 4;
const RMMSG_DANGER = 4;
const RMMSG_OTHER = 5;

// Render output
require RMCPATH . '/include/render-output.php';

// Legacy Autoloader
require RMCPATH . '/include/legacy-autoloader.php';

// Logger and Database
require_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
require_once XOOPS_ROOT_PATH . '/class/database/databasefactory.php';

$dbF = new XoopsDatabaseFactory();
$db = $dbF->getDatabaseConnection();

$GLOBALS['rmFunctions'] = new RMFunctions();
global $rmFunctions;

/**
 * New object to manage Common Utilities configurations
 */
global $cuSettings;
$GLOBALS['cuSettings'] = (object)$rmFunctions->settings->cu_settings();

/**
 * Do not use $rmc_config, use $cuSettings instead
 * @Todo: Delete references to $rmc_config
 * @deprecated
 */
$GLOBALS['rmc_config'] = (array)$cuSettings;
global $rmc_config;

// PSR-4 implementation
require __DIR__ . '/class/Psr4ClassLoader.php';
$loader = new \Common\Core\Psr4ClassLoader();
$loader->register();

// Basic namespaces from Common Utilities
$loader->addNamespace('Common\Core', RMCPATH . '/class');
$loader->addNamespace('Common\API', RMCPATH . '/api');
$loader->addNamespace('Common\API\Editors', RMCPATH . '/api/editors');
$loader->addNamespace('Common\Core\Helpers', RMCPATH . '/class/helpers');
$loader->addNamespace('Common\\Admin\\Theme\\' . ucfirst($cuSettings->theme), RMCPATH . '/themes/' . $cuSettings->theme . '/class');
//$loader->addNamespace('Common\Plugin', XOOPS_ROOT_PATH . '/modules/rmcommon/plugins');
$loader->addNamespace('Common\Widgets', XOOPS_ROOT_PATH . '/modules/rmcommon/widgets');

// Base classes
$GLOBALS['rmEvents'] = RMEvents::get();
$GLOBALS['rmTpl'] = RMTemplate::getInstance();
$GLOBALS['rmCodes'] = RMCustomCode::get();

global $rmEvents, $rmTpl, $rmCodes;

// Custom Codes
require RMCPATH . '/include/custom-codes.php';

$cuSettings->lang = $rmEvents->run_event('rmcommon.set.language', $cuSettings->lang);

// Load plugins
$file = XOOPS_CACHE_PATH . '/plgs.cnf';
$plugins = [];
$GLOBALS['installed_plugins'] = [];

if (file_exists($file)) {
  $plugins = json_decode(file_get_contents($file), true);
}

if (empty($plugins) || !is_array($plugins)) {
  $result = $db->query('SELECT dir FROM ' . $db->prefix('mod_rmcommon_plugins') . ' WHERE status=1');
  while (false !== ($row = $db->fetchArray($result))) {
    $GLOBALS['installed_plugins'][$row['dir']] = true;
    $plugins[] = $row['dir'];
    $rmEvents->load_extra_preloads(RMCPATH . '/plugins/' . $row['dir'], preg_replace('/[^A-Za-z0-9]/', '', $row['dir']) . 'Plugin');
  }
  file_put_contents($file, json_encode($plugins));
} else {
  foreach ($plugins as $p) {
    $GLOBALS['installed_plugins'][$p] = true;
    $rmEvents->load_extra_preloads(RMCPATH . '/plugins/' . $p, ucfirst(preg_replace('/[^A-Za-z0-9]/', '', $p)) . 'Plugin');
  }
}

// Load GUI theme events
$rmEvents->load_extra_preloads(RMCPATH . '/themes/' . $cuSettings->theme, ucfirst($cuSettings->theme));

// Load theme Events
list($theme) = $db->fetchRow($db->query('select conf_value from ' . $db->prefix('config') . " where conf_name = 'theme_set' and conf_modid = 0;"));
RMEvents::get()->load_extra_preloads(XOOPS_THEME_PATH . '/' . $theme, ucfirst($theme) . 'Theme');

/**
 * Modules and other elements can use this event to add their own namespaces
 * The module most return the $loader object again to prevent errors
 */
$loader = RMEvents::get()->trigger('rmcommon.psr4loader', $loader);

unset($plugins);
unset($file);

$GLOBALS['installed_plugins'] = $rmEvents->run_event('rmcommon.plugins.loaded', $GLOBALS['installed_plugins']);

require_once __DIR__ . '/api/l10n.php';

// Load rmcommon language
load_mod_locale('rmcommon');

if (isset($xoopsModule) && is_object($xoopsModule) && 'rmcommon' != $xoopsModule->dirname()) {
  load_mod_locale($xoopsModule->dirname());
}

if (!$cuSettings) {
  _e('Sorry, Red Mexico Common Utilities has not been installed yet!');
  die();
}

$rmEvents->run_event('rmcommon.base.loaded');

$rmTpl->add_head_script('var xoUrl = "' . XOOPS_URL . '";');

if ($cuSettings->updates && isset($xoopsOption['pagetype']) && 'admin' == $xoopsOption['pagetype']) {
  $interval = $cuSettings->updatesinterval <= 0 ? 7 : $cuSettings->updatesinterval;
  if (file_exists(XOOPS_CACHE_PATH . '/updates.chk')) {
    $updates = unserialize(base64_decode(file_get_contents(XOOPS_CACHE_PATH . '/updates.chk'), true));
  } else {
    $updates = ['date' => 0, 'total' => 0, 'updates' => []];
  }

  RMTemplate::getInstance()->add_script('updates.js', 'rmcommon', ['footer' => 1]);

  if ($updates['date'] < (time() - ($cuSettings->updatesinterval * 86400))) {
    $rmTpl->add_inline_script('(function(){rmCheckUpdates();})();', 1);
    define('RMC_CHECK_UPDATES', 1);
  } else {
    $rmTpl->add_inline_script('$(document).ready(function(){rmCallNotifier(' . $updates['total'] . ');});', 1);
  }
}

/**
 * Add ajax controller script
 */
if (defined('XOOPS_CPFUNC_LOADED') || (isset($xoopsOption) && array_key_exists('pagetype', $xoopsOption) && 'admin' == $xoopsOption['pagetype'])) {
  define('CU_ADMIN_SECTION', true);
  $rmTpl->add_script('cu-handler.js', 'rmcommon', ['footer' => 1, 'id' => 'cuhandler']);
  $rmTpl->add_script('jquery.validate.min.js', 'rmcommon', ['footer' => 1]);
}

// Services Manager
$cuServices = Common\Core\Helpers\Services::getInstance();
$GLOBALS['cuServices'] = $cuServices;

// Icons manager
$cuIcons = Common\Core\Helpers\Icons::getInstance();
$GLOBALS['cuIcons'] = $cuIcons;

// Common handler
global $common;
$common = Common\Core\Helpers\Common::getInstance();

// Rewrite for JS
RMSettings::write_rewrite_js();

require_once RMCPATH . '/include/tpl_functions.php';

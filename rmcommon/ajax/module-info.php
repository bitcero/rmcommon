<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: http://www.redmexico.com.mx
 */

require dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';

$ajax = new Rmcommon_Ajax();
$ajax->prepare_ajax_response();

$dirname = RMHttpRequest::get('module', 'string', '');
if ($dirname == '') {
    $ajax->ajax_response(
        __('Please specify a valid module dirname!', 'rmcommon'),
        1,
        0
    );
}

$module = RMModules::load_module($dirname);
if (!$module) {
    $ajax->ajax_response(
        __('Specified module is not installed!', 'rmcommon'),
        1,
        0
    );
}

$url =& $module->getInfo('updateurl');
$url .= false === strpos($url, '?') ? '?' : '&';
$url .= 'action=data&id=' . $module->dirname();

echo file_get_contents($url);

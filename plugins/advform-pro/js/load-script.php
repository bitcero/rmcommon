<?php
// $Id: load-script.php 11044 2013-02-13 04:54:14Z bitc3r0 $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------
header('Content-Type: text/javascript');
/**
* This file allows to include specific scripts
*/
error_reporting(0);
$xoopsOption['nocommon'] = 1;
require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/mainfile.php';

$script = isset($_GET['script']) ? $_GET['script'] : '';
if($script=='') die();

switch($script){
    case 'webfonts':
        $fonts = file_get_contents(XOOPS_VAR_PATH.'/caches/xoops_cache/webfonts.fon');
        echo 'var rmwebfonts = '.($fonts!='' ? $fonts : 'new Array();').';';
        break;
    default:
        echo 'Not allowed';
        break;
}
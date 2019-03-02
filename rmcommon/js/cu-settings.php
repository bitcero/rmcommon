<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * This file loads some other scripts that allow to rmcommon
 * work with ajax and other stuff
 */
header('Content-type: text/javascript');
error_reporting(0);
$xoopsOption['nocommon'] = 1;
include '../../../mainfile.php';

/**
 * Load rewrite modules configuration
 */
if (file_exists(XOOPS_VAR_PATH . '/caches/xoops_cache/cu-rewrite.js')) {
    include XOOPS_VAR_PATH . '/caches/xoops_cache/cu-rewrite.js';
}

/**
 * Load the ajax modules controller
 */
include XOOPS_ROOT_PATH . '/modules/rmcommon/js/cu-handler.js';

/**
 * Load commoun utilities dialogs
 */
include XOOPS_ROOT_PATH . '/modules/rmcommon/js/cu-dialogs.js';

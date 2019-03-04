<?php
// $Id: proxy.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!isset(\Xmf\Request::getString('HTTP_REFERER', '', 'SERVER')) || '' == \Xmf\Request::getString('HTTP_REFERER', '', 'SERVER')) {
    die('Not Allowed');
}

$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'text/html';

require_once dirname(__DIR__) . '/class/proxy.php';
$proxy = new RMProxy($url, $type);
echo $proxy->get();

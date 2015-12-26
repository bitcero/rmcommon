<?php
// $Id: xoops_metas.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* This file allows to default theme manage the xoops methas 
* from $xoTheme
*/
?>
<!-- Xoops Metas -->
<?php
if (isset($GLOBALS['xoTheme'])){
	
	$xoTheme = $GLOBALS['xoTheme'];
	$name = '';
	$str = '';

	foreach (array_keys($xoTheme->metas) as $type) {
		switch ($type) {
    		case 'script':
        		foreach ($xoTheme->metas[$type] as $id => $attrs) {
        			
        			// Prevent xoops.js
        			if ($id==XOOPS_URL.'/include/xoops.js') continue;
        			
            		$str .= "<script" . $xoTheme->renderAttributes($attrs) . ">";
	                if (@$attrs['_']) {
                		$str .= "\n//<![CDATA[\n" . $attrs['_'] . "\n//]]>";
	                }
	                $str .= "</script>\n";
	            }
	            break;
	        case 'link':
        		foreach ($xoTheme->metas[$type] as $rel => $attrs) {
            		$str .= '<link rel="' . $rel . '"' . $xoTheme->renderAttributes($attrs) . " />\n";
	            }
	            break;
	        case 'stylesheet':

        		foreach ($xoTheme->metas[$type] as $attrs) {

                    if ($attrs['href'] == XOOPS_URL . '/xoops.css')
                        continue;

                    if (preg_match("/^" . str_replace("/", "\\/", XOOPS_URL) . "\/language\/.*\/style.css/", $attrs['href']))
                        continue;

            		if (@$attrs['_']) {
                		$str .= '<style' . $xoTheme->renderAttributes($attrs) . ">\n/* <![CDATA[ */\n" . $attrs['_'] . "\n/* //]]> */\n</style>";
	                } else {
                		$str .= '<link rel="stylesheet"' . $xoTheme->renderAttributes($attrs) . " />\n";
	                }
	            }
	            break;
	        case 'http':
        		foreach ($xoTheme->metas[$type] as $name => $value) {
            		$str .= '<meta http-equiv="' . htmlspecialchars($name, ENT_QUOTES) . '" content="' . htmlspecialchars($value, ENT_QUOTES) . "\" />\n";
	            }
	            break;
	        default:
        		foreach ($xoTheme->metas[$type] as $name => $value) {
                    if ($name=='description'||$name=='keywords'||$name=='copyright') continue;
            		$str .= '<meta name="' . htmlspecialchars($name, ENT_QUOTES) . '" content="' . htmlspecialchars($value, ENT_QUOTES) . '" />'."\n";
	            }
	            break;
			}
	}
	echo $str;
}
?>
<!-- /End Xoops Metas -->

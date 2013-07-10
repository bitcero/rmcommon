<?php
// $Id: rmcommon.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RmcommonRmcommonPreload
{
	public function eventRmcommonLoadRightWidgets($widgets){
		
		if(!defined('RMCLOCATION')) return;
		
		include_once RMCPATH.'/include/right_widgets.php';
		
		global $xoopsModule;
		if (RMCLOCATION=='modules' && $xoopsModule->dirname()=='rmcommon' && rmc_server_var($_REQUEST, 'action', '')=='')
			$widgets[] = rmc_available_mods();
        
        if (RMCLOCATION=='blocks' && $xoopsModule->dirname()=='rmcommon'){
            //$widgets[] = rmc_blocks_new();
            //$widgets[] = rmc_blocks_addpos();
        }
		
		return $widgets;
		
	}
	
	public function eventRmcommonXoopsCommonEnd(){
		global $xoopsConfig;
        
        // Get preloaders from current theme
        RMEvents::get()->load_extra_preloads(XOOPS_THEME_PATH.'/'.$xoopsConfig['theme_set'], ucfirst($xoopsConfig['theme_set'].'Theme'));
        
		$url = RMFunctions::current_url();
		$p = parse_url($url);
        
        $config = RMFunctions::configs();
		
		if(substr($p['path'], -11)=='backend.php' && $config['rss_enable']){
			include_once RMCPATH.'/rss.php';
			die();
		}
	}
}

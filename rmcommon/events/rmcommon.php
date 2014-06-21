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
        
		$url = RMUris::current_url();
		$p = parse_url($url);
        
        $config = RMSettings::cu_settings();
		
		if(substr($p['path'], -11)=='backend.php' && $config->rss_enable){
			include_once RMCPATH.'/rss.php';
			die();
		}
	}

    /**
     * Detects when settings has changed and if the permalink
     * feature is activated.
     *
     * @param string $dirname <p>Module dirname</p>
     * @param array $save <p>Settings options saved with values</p>
     * @param array $add <p>Settings options added with values</p>
     * @param array $delete <p>Settings options deleted from database table</p>
     * @return string
     */
    public function eventRmcommonSavedSettings( $dirname, $save, $add, $delete ){

        if ( $dirname != 'rmcommon' )
            return $dirname;

        $rules = '';
        foreach ( $save['modules_path'] as $mod => $path ){

            $path = ltrim( $path, "/" );
            $rules .= "RewriteRule ^$path/?(.*)$ modules/$mod/index.php/$1 [L]\n";
            $rules .= "RewriteRule ^admin/$path/?(.*)$ modules/$mod/admin/index.php/$2 [L]\n";

        }

        if ( $save['permalinks'] == 0 ){

            $ht = new RMHtaccess('rmcommon');
            $htResult = $ht->removeRule();
            if($htResult!==true)
                showMessage(
                    __('An error ocurred while trying to delete .htaccess rules!','rmcommon') . '<br>' .
                    __('Please delete lines starting with <code># begin rmcommon</code> and ending with <code># end rmcommon</code>', 'rmcommon'), RMMSG_ERROR);

            return $dirname;

        }

        $rules .= "RewriteRule ^rss/?(.*)$ modules/rmcommon/rss.php?$1 [L]\n";

        $ht = new RMHtaccess( 'rmcommon' );
        $htResult = $ht->write( $rules );
        if($htResult!==true){
                showMessage(__('An error ocurred while trying to write .htaccess file!','rmcommon') . '<br>' .
                    __('Please try to add manually next lines:', 'rmcommon') . '<br><code>' . nl2br($rules) . '</code>', RMMSG_ERROR);
        }

        RMSettings::write_rewrite_js( $save['modules_path'] );

    }
}

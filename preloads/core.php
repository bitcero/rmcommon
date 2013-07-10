<?php
// $Id: core.php 1064 2012-09-17 16:46:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RmcommonCorePreload extends XoopsPreloadItem
{
    
    public function eventCoreHeaderStart(){
        
    }
    
    public function eventCoreHeaderEnd(){
        
        /**
        * Use internal blocks manager if enabled
        */
        $config = RMFunctions::configs();
        if($config['blocks_enable']){
            global $xoopsTpl;
            $bks = RMBlocksFunctions::construct_blocks();
            $bks = RMEvents::get()->run_event('rmcommon.retrieve.xoops.blocks', $bks);
            $b =& $xoopsTpl->get_template_vars('xoBlocks');
            $blocks = array_merge($b, $bks);
            $xoopsTpl->assign_by_ref('xoBlocks',$blocks);
            unset($b,$bks);
        }

	    RMEvents::get()->run_event('rmcommon.core.header.end');
        
    }
    
	static function eventCoreIncludeCommonStart($args){
        global $xoopsOption;
        
        if(substr($_SERVER['REQUEST_URI'], -10)=='/admin.php' && strpos($_SERVER['REQUEST_URI'], 'modules')===FALSE){
            header('location: '.XOOPS_URL.'/modules/rmcommon/');
            die();
        }
        
        if(substr($_SERVER['REQUEST_URI'], -16)=='system/admin.php' && empty($_POST)){
            header('location: '.XOOPS_URL.'/modules/rmcommon/');
            die();
        }
        
	    require_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';
		
	}
    
    /**
    * To prevent errors when upload images with closed site 
    */
    public function eventCoreIncludeCommonLanguage(){
        global $xoopsConfig;

        if($xoopsConfig['cpanel']!='redmexico'){
            $db = XoopsDatabaseFactory::getDatabaseConnection();
            $db->queryF("UPDATE ".$db->prefix("config")." SET conf_value='redmexico' WHERE conf_modid=0 AND conf_catid=1 AND conf_name='cpanel'");
        }

        if (RMFunctions::current_url()==RMCURL.'/include/upload.php' && $xoopsConfig['closesite']){
            $security = rmc_server_var($_POST, 'rmsecurity', 0);
            $data = TextCleaner::getInstance()->decrypt($security, true);
            $data = explode("|", $data); // [0] = referer, [1] = session_id(), [2] = user, [3] = token
            $xoopsUser = new XoopsUser($data[0]);
            if ($xoopsUser->isAdmin()) $xoopsConfig['closesite'] = 0;
        }
        
        RMEvents::get()->run_event('rmcommon.include.common.language');
        
    }
    
    public function eventCoreFooterStart(){
        RMEvents::get()->run_event('rmcommon.footer.start');
    }
	
    public function eventCoreFooterEnd(){
        ob_end_flush();
    }
	
    public function eventCoreClassTheme_blocksRetrieveBlocks($params){
        // xos_logos_PageBuilder
        $xpb = $params[0];
        // Template
        $tpl = $params[1];
        // Blocks
        $blocks =& $params[2];
        
	    $blocks = RMEvents::get()->run_event('rmcommon.retrieve.xoops.blocks', $blocks, $xpb, $tpl);
        	
    }

    public function eventCoreIncludeFunctionsRedirectheader($params){
		
        // 0 = URL
	    // 1 = Time
	    // 2 = Message
	    // 3 = Add redirect
	    // 4 = Allow external link
	    RMEvents::get()->run_event('rmcommon.redirect.header', $params[0], $params[1], $params[2], $params[3], $params[4]);
        if(!defined('XOOPS_CPFUNC_LOADED')) return;
        
        RMUris::redirect_with_message($params[0], $params[2]);
        die();
		
    }
	
	/**
	* RSS Management
	*/
	public function eventCoreIncludeCommonEnd(){
        global $xoopsOption;

        if(defined('RMC_CHECK_UPDATES') && $xoopsOption['pagetype']=='admin'){

            global $xoopsSecurity, $rmTpl;
            $rmTpl->add_head_script('var xoToken = "'.$xoopsSecurity->createToken().'";');
        }
		RMEvents::get()->run_event('rmcommon.xoops.common.end');
		
	}
    
    public function eventCoreHeaderAddmeta(){
        global $xoopsTpl, $xoopsConfig, $xoTheme, $rmc_config;
        
        if(!$xoopsTpl) return;
        
        $xoopsTpl->plugins_dir[] = RMCPATH.'/include';
        
    }
    
    /**
    * Next methods will add subpage to xoopsOption
    */
    public function eventCoreIndexStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'home-page';
    }
    public function eventCoreEdituserStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'edit-user';
    }
    public function eventCoreReadpmsgStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'readpm';
    }
    public function eventCoreRegisterStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'register';
    }
    public function eventCoreUserStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'user';
    }
    public function eventCoreUserinfoStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'profile';
    }
    public function eventCoreViewpmsgStart(){
        global $xoopsOption;
        $xoopsOption['module_subpage'] = 'pm';
    }
	
}

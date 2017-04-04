<?php
// $Id$
// --------------------------------------------------------------
// Red Mexico GUI for XOOPS
// A new GUI design for XOOPS, specially designed to use with RM Common
// Author: BitC3R0 <bitc3r0@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------
global $xoopsModule;

xoops_load('gui', 'system');

global $xoopsConfig, $rmc_config;
//include_once XOOPS_ROOT_PATH.'/modules/rmcommon/admin_loader.php';

if($xoopsModule && ($xoopsModule->getInfo('rmnative') || !$rmc_config['gui_disable'])){
    /**
    * XOOPS CPanel "redmexico" GUI class
    * 
    * @copyright   Red México http://redmexico.com.mx
    * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
    * @author      BitC3R0       <i.bitcero@gmail.com>
    * @version     1.0
    */
    class XoopsGuiRedmexico extends  XoopsSystemGui
    {
	    function __construct(){
		    
	    }
	    
	    static function validate(){ return true; }
	    
	    public function header(){
		    global $xoopsConfig, $xoopsUser, $xoopsModule, $xoTheme, $xoopsTpl;
		    parent::header();

            RMTemplate::getInstance()->add_jquery(false, true);
		    
		    if ($xoopsModule && !$xoopsModule->getInfo('rmnative'))
			    RMTemplate::get()->add_script(XOOPS_URL.'/include/xoops.js');
		    
	    }
	    
	    public function footer(){
		    global $xoopsConfig, $xoopsOption, $xoopsTpl, $xoTheme, $rmc_config, $xoopsModule;
            
            $xoopsLogger = XoopsLogger::getInstance();
            $xoopsLogger->stopTime('Module display');

            if (!headers_sent()) {
                header('Content-Type:text/html; charset='._CHARSET);
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Cache-Control: private, no-cache');
                header("Cache-Control: post-check=0, pre-check=0", false);
                header('Pragma: no-cache');
            }
            
            //@internal: using global $xoTheme dereferences the variable in old versions, this does not
            //if (!isset($xoTheme)) $xoTheme =& $GLOBALS['xoTheme'];
            
            if (!isset($xoTheme)) $xoTheme =& $GLOBALS['xoTheme'];

            if (isset($xoopsOption['template_main']) && $xoopsOption['template_main'] != $xoTheme->contentTemplate) {
                trigger_error("xoopsOption[template_main] should be defined before call xoops_cp_header function", E_USER_WARNING);
                if (false === strpos($xoopsOption['template_main'], ':')) {
                    $xoTheme->contentTemplate = 'db:' . $xoopsOption['template_main'];
                } else {
                    $xoTheme->contentTemplate = $xoopsOption['template_main'];
                }
            }
            
            $metas = $xoTheme->metas['script'];
            $xoTheme->metas['script'] = array();
            foreach($metas as $id => $meta){
                if(strpos($id, 'jquery/jquery.js')===FALSE && strpos($id, 'jquery/plugins/jquery.ui.js')===FALSE)
                    $xoTheme->metas['script'][$id] = $meta;
            }
            
            // Check if current theme have a replacement for template
            if(preg_match("/^db:(.*)/i",$xoTheme->contentTemplate, $match)){
                
                $file = RMCPATH.'/themes/'.$rmc_config['theme'].'/modules/'.$xoopsModule->dirname().'/'.$match[1];
                if(is_file($file))
                    $xoTheme->contentTemplate = $file;
                
            }
            
            $xoTheme->render();
            $xoopsLogger->stopTime();
            
            // RMCommon Templates
            RMTemplate::get()->footer();
            die();
            
	    }
    }
    
}
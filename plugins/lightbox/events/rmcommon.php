<?php
// $Id: rmcommon.php 838 2011-12-10 19:06:27Z i.bitcero $
// --------------------------------------------------------------
// LightBox plugin for Common Utilities
// Integrate jQuery LightBox with Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class LightboxPluginRmcommonPreload{
	
	static function eventRmcommonBaseLoaded(){
		include_once RMCPATH.'/plugins/lightbox/lightbox.php';
        RMLightbox::get();

        RMCustomCode::get()->add( 'lightbox', 'render_lightbox_element' );

	}
	
    /**
    * Replaces all ocrrencies for lightbox with the apropiate code
    * @param string $output XOOPS output
    * @return string $text Output converted 	
    *
    public function eventRmcommonEndFlush($output){
        
        if(defined('XOOPS_CPFUNC_LOADED'))
            return $output;
        
        $pattern = "/\[lightbox=(['\"]?)([^\"'<>]*)\\1](.*)\[\/lightbox\]/sU";
        $text = preg_replace_callback($pattern, 'found_lightbox', $output);
        
        $pattern = "/\[lightbox](.*)\[\/lightbox\]/sU";
        $text = preg_replace_callback($pattern, 'found_lightbox', $output);

        if(RMLightbox::get()->elements){
            
            if(!defined('RM_LB_PARAMS'))
                $script = '<script type="text/javascript" src="'.XOOPS_URL.'/modules/rmcommon/plugins/lightbox/js/jquery.colorbox-min.js"></script>'."\n";
            else
                $script = "\n";
            
            $text = str_replace("<!--LightBoxPlugin-->", $script.RMLightbox::get()->render(), $text);
        }
        
        return $text;
        
    }*/
}


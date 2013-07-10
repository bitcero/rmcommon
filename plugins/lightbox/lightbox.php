<?php
// $Id: lightbox.php 838 2011-12-10 19:06:27Z i.bitcero $
// --------------------------------------------------------------
// LightBox plugin for Common Utilities
// Integrate jQuery LightBox with Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMLightbox
{
	/**
	* Contains elements to be hanlded by lightbox
	* 
	* @var mixed
	*/
	public $elements = array();
	
	public function get(){
		static $instance;
		
		if (isset($instance))
			return $instance;
		
		$instance = new RMLightbox();
		return $instance;
	}
	
	public function __construct(){
        
		RMTemplate::get()->add_script('jquery.min.js','rmcommon', array('directory' => 'include'));
		//RMTemplate::get()->add_local_script('jquery.colorbox-min.js', 'rmcommon', 'plugins/lightbox');
		
		$config = RMFunctions::get()->plugin_settings('lightbox', true);
		
		$css = $config['theme']!='' ? $config['theme'] : 'default';
		
		RMTemplate::get()->add_style($css.'/colorbox.css', 'rmcommon', 'plugins/lightbox');
        RMTemplate::get()->add_head('<!--LightBoxPlugin-->');
	}
	
	/**
	* Elements that can be hadled by lighbox plugin.
	* eg. #container a (Will handle all "a" elements inside "#container" element)
	* eg. a.lights (will handle all "a" elements with class "lights")
	* 
	* You can provide a single element as string, or an array with all elements that you whish to hanlde
	* 
	* @param string|array $elements
	*/
	public function add_element($elements){		
		if (is_array($elements)){
			foreach ($elements as $element){
				if (in_array($element, $this->elements))
					continue;
				$this->elements[] = $element;
			}
		} else {
			if (in_array($elements, $this->elements))
				return;
			$this->elements[] = $elements;
		}
	}
	
	public function render(){
            $script = "<script type='text/javascript'>\n";
            $script .= "var lburl = '".RMCURL."/plugins/lightbox';\n";
		
            $config = RMFunctions::get()->plugin_settings('lightbox', true);
            $params = "{";
            $params .= "transition:'$config[transition]'";
            $params .= ",speed:$config[speed]";
            $params .= $config['width']!='' ? ",maxWidth:'$config[width]'" : '';
            $params .= $config['height']!='' ? ",maxHeight:'$config[height]'" : '';
            $params .= ",scalePhotos:$config[scale]";
            $params .= ",loop:".($config['loop'] ? 'true':'false');
            if($config['slideshow']){
                $params .= ",slideshow:true";
                $params .= ",slideshowSpeed:".$config['slspeed'];
                $params .= ",slideshowStart:'".__('Start Slideshow','lightbox')."'";
                $params .= ",slideshowStop:'".__('Stop Slideshow','lightbox')."'";
            }
            $params .= $config['configs']!='' ? ",$config[configs]" : '';
            $params .= "}";
		
            $script .= "var lb_params = ".$params.";\n";
            if(!defined('RM_LB_PARAMS')) define('RM_LB_PARAMS',1);
                
            $script .= "\$(function(){\n";
            if (is_array($this->elements)){
                foreach ($this->elements as $element){
                    $script .= "\$(\"$element\").colorbox(lb_params);\n";
		}
            } else {
		$script .= "\$(\"$this->elements\").colorbox(lb_params);\n";
            }
		
            $script .= "});\n</script>\n";
            
            
            RMTemplate::get()->add_script( 'jquery.colorbox-min.js', 'rmcommon', array('directory' => 'plugins/lightbox') );
        
            return $script;
	}
	
	public function __destruct(){
		//self::get()->render();
	}
}

/**
* Function to handle matches from preg_replace_callback
*/
function found_lightbox($matches){
	RMLightbox::get()->add_element('.lightbox_container '.(isset($matches[2])!='' ? $matches[2] : 'a'));
	
	return '<div class="lightbox_container">'.(isset($matches[3]) ? $matches[3] : $matches[1]).'</div>';
	
}
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
    /**
     * @var array Options to render
     */
    private $options = array();
	
	static public function get(){
		static $instance;
		
		if (isset($instance))
			return $instance;
		
		$instance = new RMLightbox();
		return $instance;
	}
	
	public function __construct(){
        
		RMTemplate::get()->add_jquery( true );
		//RMTemplate::get()->add_local_script('jquery.colorbox-min.js', 'rmcommon', 'plugins/lightbox');

        $config = RMSettings::plugin_settings( 'lightbox', true );
		
		$css = $config->theme != '' ? $config->theme : 'example1';

		RMTemplate::get()->add_style($css.'/colorbox.css', 'rmcommon', array( 'directory' => 'plugins/lightbox' ) );
        RMTemplate::get()->add_head('<!--LightBoxPlugin-->');

        // Options
        $this->options = array(
            'transition'    => $config->transition,
            'speed'         => $config->speed,
            'maxWidth'      => $config->width,
            'maxHeight'     => $config->height,
            'scalePhotos'   => $config->scale ? 'true' : 'false',
            'loop'          => $config->loop ? 'true':'false'
        );

        if($config->slideshow){
            $this->options = array_merge( $this->options, array(
                'slideshow'         => $config->slideshow ? 'true' : 'false',
                'slideshowSpeed'    => $config->slspeed,
                'slideshowAuto'     => $config->slauto ? 'true' : 'false',
                'slideshowStart'    => __('Start Slideshow','lightbox'),
                'slideshowStop'     => __('Stop Slideshow','lightbox')
            ));
        }

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

    /**
     * Add a new option to javascript
     * @param $name <p>Name of the option. When specify a existing value, then old data will be overwrite with new one.</p>
     * @param $value <p>Value for this option.</p>
     * @return bool
     */
    public function add_option( $name, $value ){

        if ( trim($name) == '' )
            return false;

        $this->options[ $name ] = $value;

        return true;

    }
	
	public function render(){
        //$script = "<script type='text/javascript'>\n";
        $script = "var lburl = '".RMCURL."/plugins/lightbox';\n";
		
        $config = RMSettings::plugin_settings( 'lightbox', true );
        $params = '';

        foreach( $this->options as $name => $value ){

            if ( $value == 'true' || $value == 'false' )
                $value = $value;
            elseif ( is_string( $value ) )
                $value = "'" . $value . "'";

            $params .= $params == '' ? "$name: $value" : ", $name: $value";

        }

        if ( $config->configs != '' )
            $params .= ", $config->configs";
		
        $script .= "var lb_params = {".$params."};\n";
        if(!defined('RM_LB_PARAMS')) define('RM_LB_PARAMS',1);
                
        $script .= "\$(function(){\n";
        if (is_array($this->elements)){
            foreach ($this->elements as $element){
                $script .= "\$(\"$element\").colorbox(lb_params);\n";
		    }
        } else {
		    $script .= "\$(\"$this->elements\").colorbox(lb_params);\n";
        }
		
        $script .= "});\n";

        RMTemplate::getInstance()->add_script('jquery.colorbox-min.js', 'rmcommon', array( 'directory' => 'plugins/lightbox' ) );
        RMTemplate::getInstance()->add_inline_script( $script, 1 );
        
        return $script;

	}
	
	public function __destruct(){
		//self::get()->render();
	}
}

/**
* Function to handle matches from preg_replace_callback
*/
function render_lightbox_element( $atts, $content ){

    $settings = RMSettings::plugin_settings( 'lighbox', true );

    $options = RMCustomCode::get()->atts( $atts, array(

        'rel'               => 'false',
        'name'              => 'lightbox-container',
        'transition'        => $settings->transition,
        'speed'             => $settings->speed,
        'maxWidth'          => $settings->width,
        'maxHeight'         => $settings->height,
        'scalePhotos'       => $settings->scale ? 'true' : 'false',
        'slideshow'         => $settings->slideshow ? 'true' : 'false',
        'slideshowSpeed'    => $settings->slspeed,
        'slideshowAuto'     => $settings->slauto ? 'true' : 'false',
        'slideshowStart'    => __('Start Slideshow','lightbox'),
        'slideshowStop'     => __('Stop Slideshow','lightbox')

    ));

    $ret = '<div class="' . $options['name'] . '">' . $content . '</div>';

	RMLightbox::get()->add_element( ".$options[name] > a" );

    foreach ( $options as $option => $value ){

        RMLightbox::get()->add_option( $option, $value );

    }

	return $ret;
	
}
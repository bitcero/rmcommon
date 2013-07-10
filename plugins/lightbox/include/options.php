<?php
// $Id: options.php 838 2011-12-10 19:06:27Z i.bitcero $
// --------------------------------------------------------------
// Lightbox plugin for Common Utilities
// Add lightbox behaviour to your links
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$options['theme'] = array(
        'caption'   =>  __('Lightbox theme','lightbox'),
        'desc'      =>  __('Select the appearance taht you wish for Lightbox plugin','lightbox'),
        'fieldtype'      =>  'select',
        'valuetype' =>  'text',
        'value'   =>  'default'
);
// Load themes
include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
$dirs = XoopsLists::getDirListAsArray(RMCPATH.'/plugins/lightbox/css/');
$opts = array();
foreach($dirs as $dir){
	if (is_file(RMCPATH.'/plugins/lightbox/css/'.$dir.'/colorbox.css'))
		$opts[$dir] = $dir;
}
$options['theme']['options'] = $opts;

// Transition type
$options['transition'] = array(
        'caption'   =>  __('Lightbox transition type','lightbox'),
        'desc'      =>  '',
        'fieldtype'      =>  'select',
        'valuetype' =>  'text',
        'value'   =>  'elastic',
        'options' => array(
        	__('Elastic','lightbox')=>'elastic',
        	__('Fade','lightbox')=>'fade',
        	__('None','lightbox')=>'none',
        )
);

// Transition speed
$options['speed'] = array(
        'caption'   =>  __('Transition speed','lightbox'),
        'desc'      =>  __('Sets the speed of the fade and elastic transitions, in milliseconds.','lightbox'),
        'fieldtype'      =>  'text',
        'valuetype' =>  'int',
        'value'   =>  '350'
);

// Max width
$options['width'] = array(
        'caption'   =>  __('Max width','lightbox'),
        'desc'      =>  __('Set a maximum width for loaded content. Example: "100%", 500, "500px". Leave 0 for no limit.','lightbox'),
        'fieldtype'      =>  'text',
        'valuetype' =>  'text',
        'value'   =>  '90%'
);

// Max Height
$options['height'] = array(
        'caption'   =>  __('Max height','lightbox'),
        'desc'      =>  __('Set a maximum height for loaded content. Example: "100%", 500, "500px". Leave 0 for no limit.','lightbox'),
        'fieldtype'      =>  'text',
        'valuetype' =>  'text',
        'value'   =>  '90%'
);

// Scale images
$options['scale'] = array(
        'caption'   =>  __('Scale images','lightbox'),
        'desc'      =>  __('If "yes" and if Max width or Max height have been defined, ColorBox will scale photos to fit within the those values.','lightbox'),
        'fieldtype'      =>  'yesno',
        'valuetype' =>  'int',
        'value'   =>  '0'
);

// Configuraciones adicionales
$options['loop'] = array(
        'caption'   =>  __('Loop images:','lightbox'),
        'desc'      =>  __('If "Yes", will disable the ability to loop back to the beginning of the group when on the last element.','lightbox'),
        'fieldtype'      =>  'yesno',
        'valuetype' =>  'int',
        'value'   =>  '0'
);

// Configuraciones adicionales
$options['slideshow'] = array(
        'caption'   =>  __('Enable slideshow:','lightbox'),
        'desc'      =>  __('If "Yes", adds an automatic slideshow to a content group / gallery.','lightbox'),
        'fieldtype'      =>  'yesno',
        'valuetype' =>  'int',
        'value'   =>  '0'
);

// Configuraciones adicionales
$options['slspeed'] = array(
        'caption'   =>  __('Slideshow speed:','lightbox'),
        'desc'      =>  __('Sets the speed of the slideshow, in milliseconds.','lightbox'),
        'fieldtype'      =>  'text',
        'valuetype' =>  'int',
        'value'   =>  '2500'
);

// Configuraciones adicionales
$options['configs'] = array(
        'caption'   =>  __('Addtitional settings','lightbox'),
        'desc'      =>  sprintf(__('Use this field to add extra configurations to lightbox. You can view all available options %s.','lightbox'), '<a href="http://colorpowered.com/colorbox/" target="_blank">'.__('here','lightbox')),
        'fieldtype'      =>  'text',
        'valuetype' =>  'text',
        'value'   =>  ''
);


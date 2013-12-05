<?php
// $Id: webfonts.class.php 11044 2013-02-13 04:54:14Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* This class allows to show a field with Webfonts from Google
*/

class RMFormWebfonts extends RMFormElement
{
    private $selected = null;
    
    /**
    * @param string $caption
    * @param string Unique id
    * @param int Initial selected option
    * @return RMFormWebfonts
    */
    public function __construct($caption, $name, $selected = null){
        $this->setCaption($caption);
        $this->setName($name);
        $this->selected = $selected;
    }
    
    /**
    * Render the control
    */
    public function render(){

        $tpl = RMTemplate::get();
        $tpl->add_head('<link rel="stylesheet" type="text/css" media="all" href="" id="webfont-previewer-'.$this->id().'" />');
        
        $hdrs = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: "._LANGCODE."\r\n" .
                                    "Referer: \r\n"
            )
        );
        
        if(ini_get('allow_url_fopen')==1){
            if(!file_exists(XOOPS_CACHE_PATH.'/webfonts.fon') || file_get_contents(XOOPS_CACHE_PATH.'/webfonts.fon')=='')
                file_put_contents(XOOPS_CACHE_PATH.'/webfonts.fon', file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyDGUH6pOxOO96PF3C1xjAucyoATpJAxA7U'));
            elseif (filemtime(XOOPS_CACHE_PATH.'/webfonts.fon')<(time()-(7*86400)))
                file_put_contents(XOOPS_CACHE_PATH.'/webfonts.fon', file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyDGUH6pOxOO96PF3C1xjAucyoATpJAxA7U'));
        } elseif(!file_exists(XOOPS_CACHE_PATH.'/webfonts.fon') || file_get_contents(XOOPS_CACHE_PATH.'/webfonts.fon')=='') {
            file_put_contents(XOOPS_CACHE_PATH.'/webfonts.fon', file_get_contents(RMCPATH.'/plugins/advform/webfonts.fon'));
        }

        $fonts = json_decode(file_get_contents(XOOPS_CACHE_PATH.'/webfonts.fon'), true);
        
        $rtn = '<div class="rm_webfonts_container" id="webfont-'.$this->id().'">';
        $rtn .= '<select name="selector-'.$this->getName().'" id="selector-'.$this->id().'" class="form-control">';
        $rtn .= '<option value="">'.__('Select font...','advform').'</option>';
        foreach($fonts['items'] as $font){
            $rtn .= '<option value="'.(str_replace(" ", "+", $font['family'])).'">'.$font['family'].'</option>';
        }
        $rtn .= "</select>";
        $rtn .= ini_get('allow_url_fopen')!=1 ? __('fopen_wrappers are disabled. Webfonts could not retrieved!','advform') : '';
        $rtn .= '<div class="font-variants"><h6>'.__('Choose Styles','advform').'</h6><div></div></div>';
        $rtn .= '<div class="font-subsets"><h6>'.__('Choose Character Sets','advform').'</h6><div></div></div>';
        $rtn .= '<div class="font-preview"><h6>'.__('Font Preview','advform').'</h6><div>'.__('This is the font preview area.','advform').'</div></div>';
        $rtn .= '<div class="font-value"><h6>'.__('Selected Font String','advform').'</h6><div></div></div>';
        $rtn .= '<div class="font-use"><h6>'.__('Font use:','advform').'</h6><div></div></div>';
        $rtn .= '<input type="hidden" name="'.$this->getName().'" id="'.$this->id().'" value="'.$this->selected.'" /></div>';
        return $rtn;
        
    }
}
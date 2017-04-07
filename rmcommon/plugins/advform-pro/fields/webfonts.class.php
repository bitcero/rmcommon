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
    public function __construct($caption, $name = '', $value = null){

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            if(null != $value){
                $this->set('value', $value);
            }
        }

        $this->setIfNotSet('selected', []);
        $this->setIfNotSet('id', TextCleaner::getInstance()->sweetstring($this->get('name')));

        $this->suppressList[] = 'selected';
    }
    
    /**
    * Render the control
    */
    public function render(){

        $tpl = RMTemplate::getInstance();
        //$tpl->add_head('<link rel="stylesheet" type="text/css" media="all" href="" id="webfont-previewer-'.$this->get('id').'" />');
        
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
            file_put_contents(XOOPS_CACHE_PATH.'/webfonts.fon', file_get_contents(RMCPATH.'/plugins/advform-pro/webfonts.fon'));
        }

        $fonts = json_decode(file_get_contents(XOOPS_CACHE_PATH.'/webfonts.fon'), true);
        
        $rtn = '<div class="rm_webfonts_container" id="webfont-'.$this->get('id').'">';
        $rtn .= '<link rel="stylesheet" type="text/css" media="all" href="" id="webfont-previewer-'.$this->get('id').'">';
        $rtn .= '<div class="input-group"><select name="selector-'.$this->get('id').'" id="selector-'.$this->get('id').'" class="form-control">';
        $rtn .= '<option value="">'.__('Select font...','advform-pro').'</option>';
        foreach($fonts['items'] as $font){
            $rtn .= '<option value="'.(str_replace(" ", "+", $font['family'])).'">'.$font['family'].'</option>';
        }
        $rtn .= '</select><span class="input-group-btn"><button type="button" class="btn btn-show" data-status="hidden" data-id="webfont-'.$this->get('id').'"><span class="fa fa-caret-down"></span></button></span></div>';
        $rtn .= ini_get('allow_url_fopen')!=1 ? __('fopen_wrappers are disabled. Webfonts could not retrieved!','advform-pro') : '';
        $rtn .= '<div class="control font-variants"><h6>'.__('Choose Styles','advform-pro').'</h6><div></div></div>';
        $rtn .= '<div class="control font-subsets"><h6>'.__('Choose Character Sets','advform-pro').'</h6><div></div></div>';
        $rtn .= '<div class="control font-preview"><h6>'.__('Font Preview','advform-pro').'</h6><div>'.__('This is the font preview area.','advform-pro').'</div></div>';
        $rtn .= '<div class="control font-value"><h6>'.__('Selected Font String','advform-pro').'</h6><div></div></div>';
        $rtn .= '<div class="control font-use"><h6>'.__('Font use:','advform-pro').'</h6><div></div></div>';
        $rtn .= '<input type="hidden" name="'.$this->get('name').'" id="'.$this->get('id').'" value="'.$this->get('value').'">';
        $rtn .= '<input type="hidden" name="'.$this->get('name').'_name" id="selector-'.$this->get('id').'-name" value="">';
        $rtn .= '</div>';
        return $rtn;
        
    }
}
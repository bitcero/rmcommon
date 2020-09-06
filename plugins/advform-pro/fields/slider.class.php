<?php
// $Id: slider.class.php 11036 2013-02-12 04:03:30Z bitc3r0 $
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormSlider extends RMFormElement
{
    private $default = array();
    private $fields = array();
    
    public function __construct($caption, $name, $default = array()){

        $this->suppressList[] = 'default';

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('default', $default, array());
        }

        $this->addClass('adv-slider-container slider-collapse');
        
    }
    
    public function addField($id, $element){
        $this->fields[$id] = $element;
    }
    
    public function render(){

        $attributes = $this->renderAttributeString();

        $fields = '';
        $tc = TextCleaner::getInstance();
        foreach($this->get('default') as $index => $slider){
            $fields .= '<div class="adv-one-slider" data-id="'.$index.'">';
            $fields .= '<div class="the-buttons">';
            $fields .= '<button type="button" class="close" data-id="imgurl-'.$this->get('id').'-'.$index.'">'.__('Close','advform-pro').'</button>';
            $fields .= '</div>';
            $fields .= '<div class="the-options input-group">';
            $fields .= '<span class="title form-control">'.$slider['title'].'</span>';
            $fields .= '<span class="input-group-btn"><button class="edit btn btn-default" type="button" data-id="'.$index.'"><span class="fa fa-edit"></span> '.__('Edit','adfvorm').'</button>';
            $fields .= '<button class="delete btn btn-warning" type="button" data-id="'.$index.'"><span class="fa fa-times"></span> '.__('Delete','advform-pro').'</button></span>';
            $fields .= '</div>';
            $fields .= '<div class="the-controls">';
            
            $types = $slider['adv-field-types'];
            $captions = $slider['adv-field-captions'];
            $ic = 0;
            foreach($slider as $id => $value){
                
                if($id=='adv-field-types' || $id=='adv-field-captions') continue;
                
                $fields .= '<div class="form-group"><label for="'.$this->get('id').'-'.$id.'-'.$index.'">'.$captions[$id].'</label>';
                switch($types[$id]){
                    case 'textarea':
                        $fields .= '<textarea name="'.$this->get('name').'['.$index.']['.$id.']" rows="5" class="form-control">'.$tc->specialchars($value).'</textarea>';
                        break;
                    case 'imageurl':
                        $fields .= '<div class="adv_imgurl" id="iurl-container-'.$this->get('id').'-'.$index.'"><div class="input-group">';
                        $fields .= '<input type="text" name="'.$this->get('name').'['.$index.']['.$id.']" id="imgurl-'.$this->get('id').'-'.$index.'" value="'.$tc->specialchars($value).'" size="10" class="form-control">';
                        $fields .= '<span class="adv_img_launcher input-group-btn" data-id="imgurl-'.$this->get('id').'-'.$index.'" data-title="'.__('Inser Image','advform-pro').'">
                                <button class="btn btn-default" type="button">...</button></span>';
                        $fields .= '</div>';
                        $fields .= '<div class="img-preview"><img style="display: inline-block;" id="preview-imgurl-'.$this->get('id').'-'.$index.'" src="'.$tc->specialchars($value).'" /></div></div>';
                        break;
                    case 'textbox':
                        $fields .= '<input type="text" class="form-control" name="'.$this->get('name').'['.$index.']['.$id.']"'.($ic==0?' class="the-title"':'').' value="'.$tc->specialchars($value).'" />';
                        break;
                    
                }
                $fields .= '</div><input type="hidden" name="'.$this->get('name').'['.$index.'][adv-field-types]['.$id.']" value="'.$types[$id].'">';
                $fields .= '<input type="hidden" name="'.$this->get('name').'['.$index.'][adv-field-captions]['.$id.']" value="'.$captions[$id].'">';
                $ic++;
                
            }

            $fields .= '</div>';
            $fields .= "</div>\n";
            
        }
        
        $rtn = '<div '.$attributes.'>';
        $rtn .= $fields;
        $rtn .= '<div class="data-fields">'.json_encode(array('name'=>$this->get('name'), 'fields'=>$this->fields)).'</div>';
        $rtn .= '<div class="button-add form-group text-right" style="margin-top: 6px;"><button class="btn btn-success" type="button">'.__('Add New','advform-pro').'</button></div>
                </div>';
        
        return $rtn;
        
    }
    
}


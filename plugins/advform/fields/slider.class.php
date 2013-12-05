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
        
        $this->setCaption($caption);
        $this->setName($name);
        $this->default = $default;
        
    }
    
    public function addField($id, $element){
        $this->fields[$id] = $element;
    }
    
    public function render(){
        //print_r($this->default); die();
        $fields = '';
        $tc = TextCleaner::getInstance();
        foreach($this->default as $index => $slider){
            $fields .= '<div class="adv-one-slider" data-id="'.$index.'">';
            $fields .= '<div class="the-options">';
            $fields .= '<span class="title">'.$slider['title'].'</span>';
            $fields .= '<div><button class="edit" type="button" data-id="'.$index.'">'.__('Edit','adfvorm').'</button>';
            $fields .= '<button class="delete button" type="button" data-id="'.$index.'">'.__('Delete','advform').'</button></div>';
            $fields .= '</div>';
            $fields .= '<div class="the-controls">';
            
            $types = $slider['adv-field-types'];
            $captions = $slider['adv-field-captions'];
            $ic = 0;
            foreach($slider as $id => $value){
                
                if($id=='adv-field-types' || $id=='adv-field-captions') continue;
                
                $fields .= '<label for="'.$this->id().'-'.$id.'-'.$index.'">'.$captions[$id].'</label>';
                switch($types[$id]){
                    case 'textarea':
                        $fields .= '<textarea name="'.$this->getName().'['.$index.']['.$id.']" rows="5">'.$tc->specialchars($value).'</textarea>';
                        break;
                    case 'imageurl':
                        $fields .= '<div class="adv_imgurl" id="iurl-container-'.$this->id().'-'.$index.'"><div><div>';
                        $fields .= '<div><input type="text" name="'.$this->getName().'['.$index.']['.$id.']" id="imgurl-'.$this->id().'-'.$index.'" value="'.$tc->specialchars($value).'" size="10" /></div>';
                        $fields .= '<div class="adv_img_launcher" data-id="imgurl-'.$this->id().'-'.$index.'" data-title="'.__('Inser Image','advform').'">...</div>';
                        $fields .= '</div></div>';
                        $fields .= '<div class="img-preview"><img style="display: inline-block;" id="preview-imgurl-'.$this->id().'-'.$index.'" src="'.$tc->specialchars($value).'" /></div></div>';
                        break;
                    case 'textbox':
                        $fields .= '<input type="text" name="'.$this->getName().'['.$index.']['.$id.']"'.($ic==0?' class="the-title"':'').' value="'.$tc->specialchars($value).'" />';
                        break;
                    
                }
                $fields .= '<input type="hidden" name="'.$this->getName().'['.$index.'][adv-field-types]['.$id.']" value="'.$types[$id].'">';
                $fields .= '<input type="hidden" name="'.$this->getName().'['.$index.'][adv-field-captions]['.$id.']" value="'.$captions[$id].'">';
                $ic++;
                
            }
            
            $fields .= '</div>';
            $fields .= '<div class="the-buttons">';
            $fields .= '<button type="button" class="close button buttonPurple" data-id="imgurl-'.$this->id().'-'.$index.'">'.__('Close','advform').'</button>';
            $fields .= '</div>';
            $fields .= "</div>\n";
            
        }
        
        $rtn = '<div class="adv-slider-container slider-collapse" id="'.$this->id().'">';
        $rtn .= $fields;
        $rtn .= '<div class="data-fields">'.json_encode(array('name'=>$this->getName(), 'fields'=>$this->fields)).'</div>';
        $rtn .= '<div class="button-add"><button class="button buttonOrange" type="button">'.__('Add New','advform').'</button></div>
                </div>';
        
        return $rtn;
        
    }
    
}


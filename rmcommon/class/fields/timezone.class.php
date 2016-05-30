<?php
// $Id: timezone.class.php 870 2011-12-22 08:51:07Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormTimeZoneField extends RMFormElement
{
	private $multi = 0;
	private $type = 0;
	private $selected = null;
	private $size = 5;

	public function __construct($caption, $name, $type = 0, $multi = 0, $selected = null, $size=5){

        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('type', $type == 0 ? 'select' : ($multi == 0 ? 'radio' : 'checkbox'), 'select');
            if($multi == 1){
                $this->setWithDefaults('multiple', null);
            }
            $this->setWithDefaults('selected', $selected, []);
        }

        $this->setIfNotSet('class', 'form-control');

        $this->suppressRender(['caption','name','multiple','selected']);
	}
	public function multi(){
		return $this->multi;
	}
	public function setMulti($value){
		return $this->multi = $value;
	}
	public function type(){
		return $this->type;
	}
	public function setType($value){
		return $this->type = $value;
	}
	public function selected(){
		return $this->selected;
	}
	public function setSelected($value){
		return $this->selected = $value;
	}
	public function size(){
		return $this->size;
	}
	public function setSize($value){
		return $this->size = $value;
	}

	public function render(){
		include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
		$zonas = XoopsLists::getTimeZoneList();
        $selected = $this->get('selected');

		if ($this->get('type') == 'readio' || $this->get('type') == 'checkbox'){

            $this->suppressRender('class');
            $attributes = $this->renderAttributeString();

			$rtn = "<div class='checkbox'>";
			foreach ($zonas as $k => $v){
				if ($this->has('multiple')){
					if (!is_array($selected)) $selected=array($selected);
					$rtn .= "<label><input $attributes value='$k' ".(is_array($selected) ? (in_array($k, $selected) ? " checked='checked'" : '') : '')."> $v</label>";
				} else {
					$rtn .= "<label><input $attributes value='$k' ".($k == $selected ? " checked='checked'" : '')."> $v</label>";
				}
				$i++;
			}
			$rtn .= "</div>";
		} else {

            $attributes = $this->renderAttributeString();

            if (!is_array($selected)) $selected=array($selected);
            $rtn = "<select $attributes>";
            foreach ($zonas as $k => $v){
                $rtn .= "<option value='$k'".(is_array($selected) ? (in_array($k, $selected) ? " selected='selected'" : '') : '').">$v</option>";
            }
            $rtn .= "</select>";
		}

		return $rtn;

	}

}


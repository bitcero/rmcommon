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
		$this->setName($name);
		$this->setCaption($caption);
		$this->multi = $multi;
		$this->type = $type;
		$this->selected = $selected;
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

		if ($this->type){
			$rtn = "<table cellpadding='2' cellspacing='1' border='0'>";
			foreach ($zonas as $k => $v){
				$rtn .= "<tr><td>";
				if ($this->multi){
					if (!is_array($this->selected)) $this->selected=array($this->selected);
					$rtn .= "<label><input type='checkbox' value='$k' name='".$this->getName()."[]' id='".$this->id()."[]'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " checked='checked'" : '') : '')." /> $v</label>";
				} else {
					$rtn .= "<label><input type='radio' value='$k' name='".$this->getName()."' id='".$this->id()."'".($k == $this->selected ? " checked='checked'" : '')." /> $v</label>";
				}
				$rtn .= "</td></tr>";
				$i++;
			}
			$rtn .= "</table>";
		} else {
			if ($this->multi){
				if (!is_array($this->selected)) $this->selected=array($this->selected);
				$rtn = "<select name='".$this->getName()."[]' id='".$this->id()."[]' size='$this->size' multiple='multiple' class=\"form-control ". $this->getClass() . "\">";
				foreach ($zonas as $k => $v){
					$rtn .= "<option value='$k'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " selected='selected'" : '') : '').">$v</option>";
				}
				$rtn .= "</select>";
			} else {
				$rtn = "<select name='".$this->getName()."' id='".$this->id()."' class=\"form-control ". $this->getClass() . "\">";
				foreach ($zonas as $k => $v){
					$rtn .= "<option value='$k'".($k==$this->selected ? " selected='selected'" : '').">$v</option>";
				}
				$rtn .= "</select>";
			}
		}

		return $rtn;

	}

}


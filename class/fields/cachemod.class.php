<?php
// $Id: cachemod.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormCacheModuleField extends RMFormElement
{
	private $selected = array();
	
	public function __construct($caption, $name, $selected=null){
		$this->setName($name);
		$this->setCaption($caption);
		$this->selected = $selected;
	}
	
	public function render(){
		$module_handler =& xoops_gethandler('application');
		$modules =& $module_handler->getObjects(new Criteria('hasmain', 1), true);
		$options = array('0' => _NOCACHE, '30' => sprintf(_SECONDS, 30), '60' => _MINUTE, '300' => sprintf(_MINUTES, 5), '1800' => sprintf(_MINUTES, 30), '3600' => _HOUR, '18000' => sprintf(_HOURS, 5), '86400' => _DAY, '259200' => sprintf(_DAYS, 3), '604800' => _WEEK);
		
		$rtn = "<table cellpadding='2' cellspacing='1' border='0'>";
		if (count($modules) > 0) {
			foreach ($modules as $mod) {
				$rtn .= "<tr><td>".$mod->getVar('name')."</td><td>
						 <select name='".$this->getName()."[".$mod->getVar('mid')."]' id='".$this->id()."'>";
				foreach ($options as $k => $v){
					$rtn .= "<option value='$k'";
					$rtn .= isset($this->selected[$mod->getVar('mid')]) && $this->selected[$mod->getVar('mid')]==$k ? " selected='selected'" : ""; 
					$rtn .= ">$v</option>";
				}
				$rtn .= "</td></tr>";
				/*$c_val = isset($currrent_val[$mid]) ? intval($currrent_val[$mid]) : null;
				$selform = new XoopsFormSelect($modules[$mid]->getVar('name'), $config[$i]->getVar('conf_name')."[$mid]", $c_val);
				$selform->addOptionArray($cache_options);
				$ele->addElement($selform);
				unset($selform);*/
			}
		} else {
			$rtn .= "<tr><td>"._AS_SYSPREF_NOMODS."</td></tr>";
		}
		
		$rtn .= "</table>";
		
		return $rtn;
	}
	
}

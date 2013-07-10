<?php
// $Id: date.class.php 870 2011-12-22 08:51:07Z i.bitcero $
// --------------------------------------------------------------
// EXM System
// Content Management System
// Author: Eduardo Cortés (aka BitC3R0)
// Email: bitc3r0@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

global $rmc_config;

class RMFormDate extends RMFormElement
{
	
	private $_date = 0;
	private $options = 0;
    private $year_range = '';
    private $time;

        /**
      * Constructor
      * @param <string> $caption
      * @param <string> $name Nombre identificador del campo
      * @param <string> $date Fecha en formato 'yyyy-mm-14'
      * @param string Year range (eg. 2000:2020)
      * @param int Show time and time format (0 = Hide, 1 = Show date and time, 2 = Show only time)
      */
	function __construct($caption, $name, $date='', $year_range='', $time=0){
		$this->setCaption($caption);
		$this->setName($name);
		$this->_date = $date;
        $this->year_range = $year_range=='' ? (date('Y',time()) - 15).':'.(date('Y',time()) + 15) : $year_range;
        
        if ($time==0) $this->options = "showHour: false, showMinute: false, showSecond: false";
        $this->time = $time;
        
        RMTemplate::get()->add_script('jquery-ui-timepicker-addon.js', 'rmcommon', array('directory' => 'include'));
        RMTemplate::get()->add_Script('dates.js', 'rmcommon', array('directory' => 'include'));

	}
    
    /**
    * Set options for widget
    * See documentation in http://trentrichardson.com/examples/timepicker/
    * @param string Options in javascript format (eg. showHour: false, showMinute: false)
    */
    public function options($options){
        
        $this->options = $options;
        
    }
	
	public function render(){
		global $exmConfig;
                /*$rtn = '';
		$rtn .= '<div class="exmDateField">
                     <div id="txt_'.$this->getName().'" class="exmTextDate">'.($this->_date===null ? _RMS_CFD_SDTXT : date($this->_showtime ? $exmConfig['datestring'] : $exmConfig['dateshort'], $this->_date)).'</div>';
		$rtn .= '<img title="'._RMS_CFD_CLICKTOSEL.'" src="'.ABSURL.'/rmcommon/images/calendar.png" alt="" class="exmfield_date_show_date" onclick="showEXMDates(\''.$this->getName().'\','.$this->_showtime.','.($this->_date===null ? "'time'" : $this->_date).');" style="cursor: pointer;" />';
		$rtn .= '<img title="'._RMS_CFD_CLICKTOCLEAR.'" src="'.ABSURL.'/rmcommon/images/calendardel.png" alt="" onclick="clearEXMDates(\''.$this->getName().'\');" style="cursor: pointer;" />';
		$rtn .= '<input type="hidden" name="'.$this->getName().'" id="'.$this->getName().'" value="'.($this->_date===null ? '' : $this->_date).'" /></div>';
		if (!defined('RM_FRAME_DATETIME_DIVOK')){
			$rtn .= "<div id='exmDatesContainer' class='exmDates'></div>";
			define('RM_FRAME_DATETIME_DIVOK', 1);
		}*/
        
        if ($this->time==1){
                RMTemplate::get()->add_head("\n<script type='text/javascript'>
                \nvar ".$this->id()."_time = 1;
            \n$(function(){
            \n$(\"#exmdate-".$this->getName()."\").datetimepicker({changeMonth: true,changeYear: true, yearRange: '".$this->year_range."'".($this->options!=''?",".$this->options:"")."});
            \n});\n</script>
            \n");
            
            $date = '';
            if($this->_date>0) $date = date('m/d/Y H:i:s', $this->_date);
            
        } elseif($this->time==0) {
            RMTemplate::get()->add_head("\n<script type='text/javascript'>
            \nvar ".$this->getName()."_time = 0;
            \n$(function(){
            \n$(\"#exmdate-".$this->id()."\").datepicker({changeMonth: true,changeYear: true, yearRange: '".$this->year_range."'".($this->options!=''?",".$this->options:"")."});
            \n});\n</script>
            \n");
            
            $date = '';
            if($this->_date>0) $date = date('m/d/Y', $this->_date);
            
        } elseif($this->time==2){
            RMTemplate::get()->add_head("\n<script type='text/javascript'>
            \nvar ".$this->getName()."_time = 2;
            \n$(function(){
            \n$(\"#exmdate-".$this->id()."\").timepicker({changeMonth: true,changeYear: true, yearRange: '".$this->year_range."'".($this->options!=''?",".$this->options:"").", timeOnlyTitle: '".__('Choose Time','rmcommon')."'});
            \n});\n</script>
            \n");
            
            $date = '';
            if($this->_date!='') $date = $this->_date;
            
        }
        
        
        
        $rtn = "<input type='text' class='exmdates_field' name='text_".$this->getName()."' id=\"exmdate-".$this->id()."\"' size='20' maxlength='19' value='".$date."' />
                    <input type='hidden' name='".$this->getName()."' id='".$this->id()."' value='".$this->_date."' />";
		return $rtn;
	}
	
}

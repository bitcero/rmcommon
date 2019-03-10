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
     *
     * <pre>
     * new RMFormDate([
     *      'caption' => 'field-caption',
     *      'name' => 'field_name',
     *      'value' => 'yyyy-mm-dd',
     *      'yearRange' => 'YYYY:YYYY',
     *      'options' => 'time, date-time or date'
     * ]);
     * </pre>
     *
     * @param array|string $caption
     * @param string $name Nombre identificador del campo
     * @param string $date Fecha en formato 'yyyy-mm-14'
     * @param string Year range (eg. 2000:2020)
     * @param int Show time and time format (0 = Hide, 1 = Show date and time, 2 = Show only time)
     * @param mixed $year_range
     * @param mixed $time
     */
    public function __construct($caption, $name = '', $date = '', $year_range = '', $time = 0)
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('value', $date, '');
            $this->setWithDefaults('yearRange', $year_range, (date('Y', time()) - 15) . ':' . (date('Y', time()) + 15));

            if (0 == $time) {
                $this->set('options', 'date');
            } elseif (1 == $time) {
                $this->set('options', 'date-time');
            } elseif (2 == $time) {
                $this->set('options', 'time');
                //$this->options = "showHour: false, showMinute: false, showSecond: false";
            }
        }

        $this->setIfNotSet('value', date('Y-m-d'));
        $this->setIfNotSet('options', 'date');
        $this->setIfNotSet('yearRange', (date('Y', time()) - 15) . ':' . (date('Y', time()) + 15));
        $this->add('class', 'form-control');

        $this->suppressList[] = 'options';
        $this->suppressList[] = 'yearRange';

        RMTemplate::getInstance()->add_jquery();
        RMTemplate::getInstance()->add_script('jquery-ui-timepicker-addon.js', 'rmcommon', ['directory' => 'include']);
        RMTemplate::getInstance()->add_script('dates.js', 'rmcommon');
    }

    /**
     * Set options for widget
     * See documentation in http://trentrichardson.com/examples/timepicker/
     * @param string Options in javascript format (eg. showHour: false, showMinute: false)
     * @param mixed $options
     */
    public function options($options)
    {
        $this->set('options', $options);
    }

    public function render()
    {
        global $exmConfig;

        $attrs = $this->renderAttributeString();

        if ('date-time' == $this->get('options')) {
            RMTemplate::getInstance()->add_inline_script('var ' . $this->id() . "_time = 1;
            \n$(function(){
            \n$(\"#exmdate-" . $this->get('id') . "\").datetimepicker({changeMonth: true,changeYear: true, yearRange: '" . $this->get('yearRange') . "'});
            \n});", 1);
        } elseif ('time' == $this->get('options')) {
            RMTemplate::getInstance()->add_inline_script('var ' . $this->get('name') . "_time = 2;
            \n$(function(){
            \n$(\"#exmdate-" . $this->id() . "\").timepicker({changeMonth: true,changeYear: true, yearRange: '" . $this->get('yearRange') . "', timeOnlyTitle: '" . __('Choose Time', 'rmcommon') . "'});
            \n});", 1);
        } elseif ('date' == $this->get('options')) {
            RMTemplate::getInstance()->add_inline_script('var ' . $this->get('name') . "_time = 0;
            \n$(function(){
            \n$(\"#exmdate-" . $this->id() . "\").datepicker({changeMonth: true,changeYear: true, yearRange: '" . $this->get('yearRange') . "', showHour: false, showMinute: false, showSecond: false});
            \n});", 1);
        }

        $rtn = "<input type='text' class='exmdates_field " . $this->getClass() . "' name='text_" . $this->get('name') . "' id=\"exmdate-" . $this->id() . "\"' size='20' maxlength='19' value='" . $this->get('value') . "'>
                    <input type='hidden' $attrs>";

        return $rtn;
    }
}

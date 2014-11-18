<?php
// $Id: pagenav.php 949 2012-04-14 04:18:10Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMPageNav
{
    private $total_results;
    private $items_page;
    private $current_page;
    private $steps = 5;
    private $url = '';
    private $showing = '';
    private $displayed = false;
    private $rendered = '';
    private $template = '';
    private $start = 0;

    /**
    * @desc Constructor
    *
    * @param int Total results number
    * @param int Items per page
    * @param int Current Page
    * @param int Steps to show when number of pages exceed the specified number
    */
    function __construct($total_results, $items_page, $current_page, $steps=5){
        $this->total_results = $total_results;
        $this->items_page = $items_page;
        $this->current_page = $current_page;
        $this->steps = $steps;
        RMTemplate::get()->add_style('pagenav.css', 'rmcommon');

        $total_pages = ceil($total_results / $items_page);
        $current_page = $current_page > $total_pages ? $total_pages : $current_page;
        $current_page = $current_page==0 ? 1: $current_page;

        $start = 1;
        $this->start = $items_page * $current_page - $items_page;
    }

    /**
    * Set or gets the url for pages.
    * URL must contain the string {PAGE_NUM} that will replaced for the page number
    * (eg. /somepage.php?param=something&page={PAGE_NUM})
    *
    * @param string Target url
    * @return string
    */
    public function target_url(){
		$num = func_num_args();
		if ($num>0){
			$url = func_get_arg(0);
			$this->url = $url;
		} else {
			return $this->url;
		}
        return null;
    }

    /**
    * Set or gets the step numbers
    *
    * @param int Steps number
    * @return int
    */
    public function steps(){
		$num = func_num_args();
		if ($num>0){
			$steps = func_get_arg(0);
			$this->steps = $steps;
		} else {
			return $this->steps;
		}
        return null;
    }

    /**
    * Gets the text "Showing..."
    */
    public function get_showing(){
		return $this->showing;
    }

    /**
    * Get start number
    */
    public function start(){
        return $this->start;
    }

    /**
     * Set the template to use with render
     * @param string Path to template file
     */
    public function set_template($path){
        $this->template = $path;
    }

    /**
    * This method render the navigation bar with pages and all information.
    * Also creates the message "Shogin ..." that can be get after.
    *
    * @param bool INdicates if this method must show the navbar or only render it
    * @return string|echo
    */
    public function render($caption, $showing=0){

		// If we have the content of render then return it
		if ($this->displayed){
			return $this->rendered;
		}

		$total_pages = ceil($this->total_results / $this->items_page);
		$current_page = $this->current_page > $total_pages ? $total_pages : $this->current_page;
        $current_page = $current_page==0 ? 1: $current_page;
		$items_page = $this->items_page;
		$total_results = $this->total_results;
		$steps = $this->steps();
		$url = $this->url;

        if ($current_page==1){
            $first_element = 1;
        } else{
            $first_element = $total_results > 0 ? (($current_page * $items_page) - ($items_page)) + 1 : 0;
        }

		$last_element = $current_page*$items_page;
		$last_element = $last_element>$total_results ? $total_results : $last_element;
		$this->showing = sprintf(__('Showing results <strong>%u</strong> to <strong>%u</strong> from <strong>%u</strong>.','rmcommon'), $first_element, $last_element, $total_results);

		if ($total_pages<=1) return null;

		if($showing) $showing_legend = $this->showing;

		ob_start();

		$start = 1;

		if ($current_page>$steps-1){
			$start = $current_page-floor($steps/2);
		}
		//echo "$start>($total_pages - $steps) ? ($total_pages - $steps)+1 : $start;"; die();
		$start = $start>=($total_pages - $steps) && $start!=1 ? ($total_pages - $steps)+1 : $start;
		$start = $start<1 ? 1 : $start;
        $this->start = $start;

		$end = $start+($steps-1);
		$end = $end > $total_pages ? $total_pages : $end;

                if($this->template!='' && is_file($this->template)){
                    include $this->template;
                } else {
                    include RMTemplate::get()->get_template('rmc-navigation-pages.php', 'module', 'rmcommon');
                }

		$this->displayed = true;

		$this->rendered = ob_get_clean();
		return $this->rendered;

    }

    /**
    * Displays the navbar
    */
    public function display($caption = true, $showing = 0){
		echo $this->render($caption, $showing);
    }

}

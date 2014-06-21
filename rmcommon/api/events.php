<?php
// $Id: events.php 1049 2012-09-11 19:40:00Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!defined('XOOPS_ROOT_PATH')) die("Sorry, there are not <a href='../'>nothing here</a>");

//include_once 'langs/english.php';

include_once RMCPATH.'/class/textcleaner.php';

class RMEvents
{
    
    private $_events = array();
    private $_preloads = array();
    
    public function __construct(){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        
        $result = $db->query("SELECT dirname FROM ".$db->prefix("modules")." WHERE isactive='1'");
        
        $i = 0;
        while (list($module) = $db->fetchRow($result)) {
        	if (is_dir($dir = XOOPS_ROOT_PATH . "/modules/{$module}/events/")) {
            	$file_list = XoopsLists::getFileListAsArray($dir);
                foreach ($file_list as $file) {
                	if (preg_match('/(\.php)$/i', $file)) {
                    	$file = substr($file, 0, -4);
                        $this->_preloads[$i]['module'] = $module;
                        $this->_preloads[$i]['file'] = $file;
                        $i++;
                    }
                }
            }
        }
        
        
        // Set Events
        foreach ($this->_preloads as $preload) {
            include_once XOOPS_ROOT_PATH . '/modules/' . $preload['module'] . '/events/' . $preload['file']. '.php';
            $class_name = ucfirst($preload['module']) . ucfirst($preload['file']) . 'Preload' ;
            if (!class_exists($class_name)) {
                continue;
            }
            $class_methods = get_class_methods($class_name);
            foreach ($class_methods as $method) {
                if (strpos($method, 'event') === 0) {
                    $event_name = strtolower(str_replace('event', '', $method));
                    $event= array('class_name' => $class_name, 'method' => $method);
                    $this->_events[$event_name][] = $event;
                }
            }
        }
        
    }
    
    /**
    * Get an singleton instance for events api
    */
    static function get(){
        static $instance;
        
        if (isset($instance))
            return $instance;
        
        $instance = new RMEvents();
        return $instance;
    }
    
    public function load_extra_preloads($dir, $name){
        $dir = rtrim($dir, '/');
        $extra = array();
         if (is_dir($dir.'/events')){
            $file_list = XoopsLists::getFileListAsArray($dir.'/events');
            foreach ($file_list as $file) {
                if (preg_match('/(\.php)$/i', $file)) {
                    $file = substr($file, 0, -4);
                    $extra[] = $file;
                }
            }
        }
        
        foreach ($extra as $preload) {
            include_once $dir . '/events/' . $preload. '.php';
            $class_name = ucfirst($name) . ucfirst($preload) . 'Preload' ;
            if (!class_exists($class_name)) {
                continue;
            }
            $class_methods = get_class_methods($class_name);
            foreach ($class_methods as $method) {
                if (strpos($method, 'event') === 0) {
                    $event_name = strtolower(str_replace('event', '', $method));
                    $event= array('class_name' => $class_name, 'method' => $method);
                    $this->_events[$event_name][] = $event;
                }
            }
        }
        
    }
    
	/**
	* @desc Almacena toda la información de la API
	*/
    public function run_event($event_name, $value=null)
    {
        $pre = $event_name;
        $event_name = strtolower(str_replace('.', '', $event_name));        
        $args = func_get_args();
        if (!isset($this->_events[$event_name])) return $value;
        
        $xoopsLogger = XoopsLogger::getInstance();
        
        foreach ($this->_events[$event_name] as $event) {
            $args[1] =& $value;
            $xoopsLogger->addExtra($pre, $event['class_name'].'::'.$event['method']);
            $value = call_user_func_array(array($event['class_name'], $event['method']), array_slice($args, 1));
        }
        return $value;
    }

}

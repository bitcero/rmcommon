<?php
// $Id: tinyeditor.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* This files allow to exigent people to control every aspect of tinymce
* from exmsystem
*/
class TinyEditor
{
    public $configuration = array();

    static function getInstance(){
        static $instance;
        if (!isset($instance)) {
            $instance = new TinyEditor();
        }
        return $instance;
    }

    // Configuración
    public function add_config($names,$values, $replace=false){
    	if (is_array($names) && is_array($values)){
	    	foreach ($names as $i => $name){
	            // Replace if needed
	            if ($replace){
	            	$this->configuration[$name] = $values[$i];
				} else {
		            // Not replace, verify...
	            	$this->configuration[$name] = isset($this->configuration[$name]) ? ",".$values[$i] : $values[$i];
				}
	    	}
	    } else {
	        if ($replace || !isset($this->configuration[$names]))
	        	$this->configuration[$names] = $values;
	        else
	        	$this->configuration[$names] .= isset($this->configuration[$names]) ? ",$values" : $values;
	    }

    }

    public function remove_config($name){
        if (empty($this->configuration)) return;

        unset($this->configuration[$name]);

    }

    public function get_js(){

        $rtn = 'function initMCE(elements){

                    tinyMCE.init({';
                        $configs = ''; $i = 0;
                        foreach ($this->configuration as $name => $value){
                            $i++;
                            if( $name == 'elements' ){
                                $configs .= $name . ': elements,' . "\n";
                            } else {
                                $configs .= $name.' : "'.$value.'"'.($i>count($this->configuration) ? '' : ',')."\n";
                            }
                        }
                        $rtn .= $configs . '
                        setup: function(ed){
                            ed.onKeyUp.add(function(ed, e){
                                if (tinyMCE.activeEditor.isDirty())
                                    ed.save();
                            });
                        },

                        oninit: function(ed){
                            switchEditors.go(elements, "'.(isset($_COOKIE['editor']) ? $_COOKIE['editor'] : 'tinymce').'");
                        }
                    });
                };

					initMCE("'.$this->configuration['elements'].'");
				';

        return $rtn;
    }
}

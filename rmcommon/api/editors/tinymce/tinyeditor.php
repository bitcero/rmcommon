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
class tinyeditor
{
    public $configuration = [];

    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    // Configuración
    public function add_config($names, $values, $replace = false)
    {
        if (is_array($names) && is_array($values)) {
            foreach ($names as $i => $name) {
                // Replace if needed
                if ($replace) {
                    $this->configuration[$name] = $values[$i];
                } else {
                    // Not replace, verify...
                    $this->configuration[$name] = isset($this->configuration[$name]) ? ',' . $values[$i] : $values[$i];
                }
            }
        } else {
            if ($replace || !isset($this->configuration[$names])) {
                $this->configuration[$names] = $values;
            } else {
                $this->configuration[$names] .= isset($this->configuration[$names]) ? ",$values" : $values;
            }
        }
    }

    public function remove_config($name)
    {
        if (empty($this->configuration)) {
            return;
        }

        unset($this->configuration[$name]);
    }

    public function get_js()
    {
        if (defined('TINY_JS_INCLUDED')) {
            return null;
        }

        define('TINY_JS_INCLUDED', 1);

        ob_start(); ?>

        (function($){
            this.initMCE = function(elements){

                var editor;

                editor = $(elements).tinymce({
                    <?php

                    $elements = $this->configuration['elements'];
        unset($this->configuration['elements']);

        echo mb_substr(json_encode($this->configuration), 1, -1) . ','; ?>
                    setup: function(ed){
                        ed.on('keyup', function(e){
                            if ($(editor).isDirty())
                                this.save();
                        });
                    },

                    oninit: function(ed){
                        switchEditors.edInit();
                        switchEditors.go(elements, "<?php echo isset($_COOKIE['editor']) ? $_COOKIE['editor'] : 'tinymce'; ?>");
                    },

                    init_instance_callback: function(editor) {

                        if(undefined == switchEditors){return false;};

                        editor.on('BeforeSetContent', function(e) {
                            e.content = switchEditors.esautop(e.content);
                        });
                    }
                });

                return editor;

            };

        })(jQuery);

        <?php $rtn = ob_get_clean();

        return $rtn;
    }
}

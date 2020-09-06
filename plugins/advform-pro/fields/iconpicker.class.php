<?php
// $Id$
// --------------------------------------------------------------
// AdvancedForm plugin for Common Utilities
// Improves rmcommon forms by adding new fields and controls
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormIconsPicker extends RMFormElement
{
    private $default = '';
    private $size = '';
    private $fa = 1;
    private $glyph = 1;

    /**
     * Class constructor
     * @param string $caption Caption to render for field
     * @param string $name Name of the field (the id will be generated from this)
     * @param array $options Array with additional options for control. Options can be:
     * <ul>
     * <li><strong>'selected'</strong>: contains the initial value for control.</li>
     * <li><strong>'size'</strong>: size of the control. Can be <ul><li>lg</li><li>sm</li><li>xs</li><li>or leave blank for default size</li></ul></li>
     * <li><strong>'fontawesome'</strong>: Can be 1 or 0 and indicate if the control must show FontAwesome icons or not (<em>Default value: 1</em>).</li>
     * <li><strong>'glyphicons'</strong>: Can be 1 or 0 and indicates if the control must show Glyphicons or not (<em>Default value: 1</em>)</li>
     * </ul>
     */
    public function __construct( $caption, $name, $options = array() ){

        $suppress = ['default', 'fa', 'glyph', 'svg', 'size', 'id'];
        $this->suppressList = array_merge($this->suppressList, $suppress);

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, '');
            $this->setWithDefaults('default', array_key_exists('selected', $options ) ? $options['selected'] : '', '');
            $this->set('fa', array_key_exists('fontawesome', $options) ? $options['fontawesome'] : true);
            $this->set('glyph', array_key_exists('glyphicons', $options) ? $options['glyphicons'] : false);
            $this->set('moon', array_key_exists('moon', $options) ? $options['moon'] : false);
            $this->set('svg', array_key_exists('svg', $options) ? $options['svg'] : true);
            $this->set('size', array_key_exists('size', $options) ? $options['size'] : '');
        }

        $this->setIfNotSet('fa', true);
        $this->setIfNotSet('glyph', false);
        $this->setIfNotSet('moon', false);
        $this->setIfNotSet('svg', true);
        $this->setIfNotSet('size', '');

        $this->addClass('adv-icons-picker');

    }

    public function render(){
        global $cuIcons;

        RMTemplate::get()->add_script( 'advanced-fields.min.js', 'rmcommon', array( 'directory' => 'plugins/advform-pro', 'footer' => 1, 'id' => 'advform-js' ) );
        RMTemplate::get()->add_style( 'advforms.min.css', 'rmcommon', array('directory' => 'plugins/advform-pro', 'id' => 'advform-css' ) );

        $attributes = $this->renderAttributeString();

        include RMCPATH . '/plugins/advform-pro/templates/icon-picker.php';

    }

}
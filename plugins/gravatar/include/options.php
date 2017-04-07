<?php
// $Id: options.php 198 2010-02-09 04:11:40Z i.bitcero $
// --------------------------------------------------------------
// Recaptcha plugin for Common Utilities
// Allows to integrate recaptcha in comments or forms
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$options['size'] = array(
        'caption'   =>  __('Avatars size in pixels','avatars'),
        'desc'      =>  '',
        'fieldtype'      =>  'textbox',
        'valuetype' =>  'int',
        'value'   =>  80
);

$options['default'] = array(
        'caption'   =>  __('Default avatar URL','avatars'),
        'desc'      =>  '',
        'fieldtype'      =>  'textbox',
        'valuetype' =>  'text',
        'value'   =>  ''
);

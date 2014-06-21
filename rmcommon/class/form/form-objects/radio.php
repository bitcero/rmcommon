<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: http://www.redmexico.com.mx
 */

class ActiveRadio
{
    use RMFormComponent;

    public function render(){

        $parameters = $this->parameters;
        $options = $parameters['options'];
        $class = isset( $parameters['class'] ) ? $parameters['class'] : '';
        $checked = isset( $parameters['checked'] ) ? $parameters['checked'] : '';
        $attributes = '';

        foreach ( $parameters as $attr => $value ){
            if ( 'class' == $attr || 'options' == $attr || 'checked' == $attr )
                continue;

            $attributes .= ' ' . $attr . '="' . $value . '"';
        }

        $input = '';
        foreach ( $options as $value => $option ){

            $input .= '<label' . ( $class != '' ? ' class="' . $class .'"' : '') . '>';
            $input .= '<input type="radio" name="' . $this->name . '" id="' . $this->id . $value . '" value="' . $value .'"';
            $input .= $this->required ? ' required' : '';
            $input .= $attributes . ($value == $checked ? ' checked="checked"' : '') . '> ' . $option . '</label>';

        }

        return $input;

    }
}
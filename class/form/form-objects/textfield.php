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

class ActiveTextField
{
    use RMFormComponent;

    public function render(){

        $input = '<input type="text" name="' . $this->name . '" id="' . $this->id . '"';
        $class = $this->required ? 'required' : '';
        $max = 0;

        $parameters = $this->parameters;

        foreach ( $parameters as $attr => $value ){
            if ( 'class' == $attr )
                $class .= $class != '' ? ' '.$value : $value;
            elseif ( 'maxlength' == $attr )
                $max = $value;
            else
                $input .= ' ' . $attr . '="' . $value . '"';
        }

        $input .= '' != $class ? ' class="' . $class . '"' : '';
        $input .= $this->required ? ' required' : '';

        $columns = $this->model->columns;
        /*$max = 0 == $max ? $columns[$this->element]['len'] : $max;
        $input .= $max > 0 ? ' maxlength="' . $max .'"' : '';*/
        $input .= '>';

        return $input;

    }

}
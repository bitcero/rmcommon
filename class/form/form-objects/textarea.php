<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Schooler Pro
 * Description: A module for management of scores and notes for students
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: Private
 * URI: http://www.redmexico.com.mx
 * --------------------------------------------------------------
 */

class ActiveTextArea
{
    use RMFormComponent;

    public function render(){

        $input = '<textarea name="' . $this->name . '" id="' . $this->element . '"';
        $class = $this->required ? 'required' : '';
        $text = '';

        $parameters = $this->parameters;

        foreach ( $parameters as $attr => $value ){
            if ( 'class' == $attr )
                $class .= $class != '' ? ' '.$value : $value;
            elseif ( 'value' == $attr )
                $text = $value;
            else
                $input .= ' ' . $attr . '="' . $value . '"';
        }

        $input .= '' != $class ? ' class="' . $class . '"' : '';
        $input .= $this->required ? ' required' : '';

        //$columns = $this->model->columns;
        $input .= '>' . $text . '</textarea>';

        return $input;

    }

}
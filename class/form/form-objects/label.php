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

/**
 * This class allows to insert a &lt;label&gt; element in form according to given parameters.
 *
 * Class FormLabel
 */
class ActiveLabel
{
    use RMFormComponent;

    public function render(){

        $label = '<label for="' . $this->id . '"';
        $class = $this->required ? 'label-required' : '';
        $title = $this->model->title($this->element);

        $parameters = $this->parameters;

        foreach ( $parameters as $attr => $value ){
            if ( 'class' == $attr )
                $class .= $class != '' ? ' '.$value : $value;
            elseif ( 'caption' == $attr )
                $title = $value;
            else
                $label .= ' ' . $attr . '="' . $value . '"';
        }

        $label .= '' != $class ? ' class="' . $class . '"' : '';

        $columns = $this->model->columns;
        $label .= '><strong>' . $title . '</strong> ';
        $label .= '</label>';

        return $label;

    }


}
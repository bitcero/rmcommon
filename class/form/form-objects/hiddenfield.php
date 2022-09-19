<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: https://bitcero.dev
 */
class ActiveHiddenField
{
    use RMFormComponent;

    public function render()
    {
        $input = '<input name="' . $this->name . '" type="hidden" id="' . $this->id . '"';
        $class = '';
        $parameters = $this->parameters;

        foreach ($parameters as $attr => $value) {
            if ('class' == $attr) {
                $class .= '' != $class ? ' ' . $value : $value;
            } else {
                $input .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $input .= '' != $class ? ' class="' . $class . '"' : '';

        $input .= '>';

        return $input;
    }
}

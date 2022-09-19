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
class ActiveButton
{
    use RMFormComponent;

    public function render()
    {
        $button = '<button id="' . $this->id . '" ';
        $class = '';
        $type = 'button';
        $caption = __('Button', 'rmcommon');

        $parameters = $this->parameters;

        foreach ($parameters as $attr => $value) {
            if ('class' == $attr) {
                $class .= '' != $class ? ' ' . $value : $value;
            } elseif ('type' == $attr) {
                $type = $value;
            } elseif ('caption' == $attr || 'value' == $attr) {
                $caption = $value;
            } else {
                $button .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $button .= '' != $class ? ' class="' . $class . '"' : '';
        $button .= ' type="' . $type . '">' . $caption . '</button>';

        return $button;
    }
}

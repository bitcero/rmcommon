<?php
/**
 * Editors Abstract Class
 * Abstract class for inheritance in Editors
 * 
 * Copyright © 2015 Eduardo Cortés https://eduardocortes.mx
 * -----------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package       rmcommon
 * @subpackage    Editors
 * @author        Eduardo Cortés <i.bitcero@gmail.com>
 * @since         2.2
 */

abstract class RMEditor
{
    protected $attributes = array();

    public function attr( $name, $value ){
        $this->attributes[ $name ] = $value;
    }

    public function get_attributes(){
        return $this->attributes;
    }

    public function render_attributes(){

        $ret = '';
        foreach( $this->attributes as $name => $value ){

            $ret .= ('' == $ret ? '' : ' ') . $name . '="' . $value . '"';

        }

        return $ret;

    }
}
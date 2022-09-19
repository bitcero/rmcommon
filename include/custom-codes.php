<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
 * -------------------------------------------------------------
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */
class EnabledCustomCodes
{
    public function cuIcon($attrs)
    {
        global $cuIcons;

        $cc = RMCustomCode::get();

        //$attrs = $cc->atts(['icon' => ''], $attrs);

        if ('' == $attrs['icon']) {
            return '';
        }

        $icon = $attrs['icon'];
        unset($attrs['icon']);

        return $cuIcons->getIcon($icon, $attrs);
    }

    /**
     * @return EnabledCustomCodes
     */
    public static function getInstance()
    {
        static $instance;

        if (isset($instance)) {
            return $instance;
        }

        $instance = new self();

        return $instance;
    }
}

$enabledCC = new EnabledCustomCodes();

/**
 * SVG icon custom code:
 * <pre>
 * [cuicon icon=svg-rmcommon-rmcommon attr1="attr1" attr2="attr2" ...]
 */
$rmCodes->add('cuicon', [$enabledCC, 'cuIcon']);

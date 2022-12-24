<?php
/**
 * Advanced Form Fields for Common Utilities
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
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/mainfile.php';
global $common;

$common->ajax()->prepare();

if(!$xoopsSecurity->check(true, false, 'CUTOKEN')){
    $common->ajax()->response(
        __('Session token expired!', 'advform-pro'), 1, 0
    );
}

// Include icons list
require 'icons-lists.php';

RMTemplate::getInstance()->assign('svgIcons', $cuIcons->iconsListByProvider());
//RMTemplate::getInstance()->assign('faIcons', $fontAwesome);
//RMTemplate::getInstance()->assign('glyphIcons', $glyphIcons);
RMTemplate::getInstance()->assign('moonIcons', $icomoon);

// Parent
$parent = RMHttpRequest::get('parent', 'string', '');
if(''==trim($parent)){
    $common->ajax()->response(
        __('Provided data is not valid: parent field is missing!', 'advform-pro'), 1, 1
    );
}

RMTemplate::getInstance()->assign('parent', $parent);
RMTemplate::getInstance()->assign('selectedIcon', RMHttpRequest::get('icon', 'string', ''));

$content = RMTemplate::getInstance()->render('icons-browser.php', 'plugin', 'rmcommon', 'advform-pro');

$common->ajax()->response(
    __('Browse Icons', 'advform-pro'), 0, 1, [
        'openDialog' => 1,
        'content' => $content,
        'width' => 'large',
        'color' => 'deep-orange'
    ]
);

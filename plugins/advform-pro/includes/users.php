<?php
/**
 * Advanced Forms Pro Plugin for Common Utilities
 *
 * Copyright © 2015 - 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

require dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/mainfile.php';

$common->ajax()->prepare();

$search = trim($common->httpRequest()->get('search', 'string', ''));
$page = trim($common->httpRequest()->get('page', 'integer', 1));
$page = $page <= 0 ? 0 : $page-1;
$start = $page * 30;

if('' == $search){
    $sql = "SELECT uid, uname, name, email FROM " . $xoopsDB->prefix('users') . " ORDER BY uname LIMIT $start, 30";
} else {
    $sql = "SELECT uid, uname, name, email FROM " . $xoopsDB->prefix("users") . " WHERE uid = " . intval($search) .
        " OR uname LIKE '%" . $xoopsDB->escape($search) . "%' OR name LIKE '%" . $xoopsDB->escape($search) . "%'" .
        " OR email LIKE '%" . $xoopsDB->escape($search) . "%' ORDER BY uname LIMIT $start, 30";
}

$result = $xoopsDB->query($sql);
$users = [];

while($row = $xoopsDB->fetchArray($result)){
    $users[] = [
        'id' => $row['uid'],
        'uname' => $row['uname'],
        'name' => $row['name'],
        'text' => '' != $row['name'] ? $row['name'] . ' (' . $row['uname'] . ')' : $row['uname'],
        'email' => $row['email'],
        'avatar' => $common->services()->service('avatar')->getAvatarSrc($row['email'], 80)
    ];
}

$common->ajax()->response(__('Found users', 'advform-pro'), 0, 1, [
    'items' => $users,
    'total' => count($users)
]);


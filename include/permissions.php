<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.eduardocortes.mx
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
$rmcommon_permissions = [
    /**
     * Modules managament privileges
     */
    'admin-modules' => [
        'caption' => __('Modules management (Install, uninstall and update)', 'rmcommon'),
        'default' => 'deny',
    ],

    'admin-images' => [
        'caption' => __('Images management', 'rmcommon'),
        'default' => 'deny',
    ],

    'admin-comments' => [
        'caption' => __('Comments management', 'rmcommon'),
        'default' => 'deny',
    ],

    'admin-plugins' => [
        'caption' => __('Plugins management (Install, uninstall and update)', 'rmcommon'),
        'default' => 'deny',
    ],

    'admin-groups' => [
        'caption' => __('Groups management (create, delete and update)', 'rmcommon'),
        'default' => 'deny',
    ],

    'configure-system' => [
        'caption' => __('Grant access to system and modules configuration.', 'rmcommon'),
        'default' => 'deny',
    ],
];

return $rmcommon_permissions;

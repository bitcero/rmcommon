<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Red Mexico https://bitcero.dev
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
 * @copyright    Red Mexico (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */
defined('XOOPS_MAINFILE_INCLUDED') || die('No access');

if (!function_exists('__')) {
    require_once XOOPS_ROOT_PATH . '/modules/rmcommon/loader.php';
}

$adminmenu[] = [
    'title' => __('Dashboard', 'rmcommon'),
    'link' => 'index.php',
    'icon' => 'svg-rmcommon-dashboard text-red',
    'location' => 'dashboard',
];

$adminmenu[] = [
    'title' => __('Modules', 'rmcommon'),
    'link' => 'modules.php',
    'icon' => 'svg-rmcommon-module text-green',
    'location' => 'modules',
];

$adminmenu[] = [
    'title' => __('Blocks', 'rmcommon'),
    'link' => 'blocks.php',
    'icon' => 'svg-rmcommon-blocks text-teal',
    'location' => 'blocks',
];

$adminmenu[] = [
    'title' => __('Users', 'rmcommon'),
    'link' => 'users.php',
    'icon' => 'svg-rmcommon-users text-blue',
    'location' => 'users',
];

$adminmenu[] = [
    'title' => __('Groups', 'rmcommon'),
    'link' => 'groups.php',
    'icon' => 'svg-rmcommon-group text-blue-grey',
    'location' => 'groups',
];

$adminmenu[] = [
    'title' => __('Images', 'rmcommon'),
    'link' => 'images.php',
    'icon' => 'svg-rmcommon-images text-cyan',
    'location' => 'imgmanager',
    'options' => [0 => [
                    'title' => __('Categories', 'rmcommon'),
                    'link' => 'images.php?action=showcats',
                    'selected' => 'rmc_imgcats',
                    'icon' => 'svg-rmcommon-folder text-orange',
            ], 1 => [
                    'title' => __('New category', 'rmcommon'),
                    'link' => 'images.php?action=newcat',
                    'selected' => 'rmc_imgnewcat',
                    'icon' => 'svg-rmcommon-folder-plus text-orange',
            ], 2 => [
                    'divider' => 1,
            ], 3 => [
                    'title' => __('Images', 'rmcommon'),
                    'link' => 'images.php',
                    'selected' => 'rmc_images',
                    'icon' => 'svg-rmcommon-camera text-success',
            ], 4 => [
                    'title' => __('Add images', 'rmcommon'),
                    'link' => 'images.php?action=new',
                    'selected' => 'rmc_newimages',
                    'icon' => 'svg-rmcommon-camera-plus text-success',
            ],
    ],
];

$adminmenu[] = [
    'title' => __('Comments', 'rmcommon'),
    'link' => 'comments.php',
    'icon' => 'svg-rmcommon-comments text-orange',
    'location' => 'comments',
];

$adminmenu[] = [
    'title' => __('Plugins', 'rmcommon'),
    'link' => 'plugins.php',
    'icon' => 'svg-rmcommon-plug text-danger',
    'location' => 'plugins',
];

$adminmenu[] = [
    'title' => __('Services', 'rmcommon'),
    'link' => 'services.php',
    'icon' => 'svg-rmcommon-services text-deep-purple',
    'location' => 'services',
];

$adminmenu[] = [
    'title' => __('Updates', 'rmcommon'),
    'link' => 'updates.php',
    'icon' => 'svg-rmcommon-update text-success',
    'location' => 'updates',
];

$adminmenu[] = [
    'title' => __('Icons', 'rmcommon'),
    'link' => 'icons.php',
    'icon' => 'svg-rmcommon-vector text-blue',
    'location' => 'icons',
];

$adminmenu[] = [
    'title' => __('About', 'rmcommon'),
    'link' => 'about.php',
    'icon' => 'svg-rmcommon-info text-info',
    'location' => 'about',
];
